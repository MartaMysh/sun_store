<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Product Catalog</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/alpinejs" defer></script>
</head>
<body class="bg-gray-50 text-gray-800">

<div class="max-w-7xl mx-auto py-10 px-4" x-data="productCatalog()">
    <h1 class="text-3xl font-bold mb-6 text-center">Product Catalog</h1>

    <!-- 🔍 Filter Form -->
    <form @submit.prevent="fetchProducts"
          class="grid grid-cols-1 md:grid-cols-4 gap-4 bg-white p-6 rounded-2xl shadow">

        <div>
            <label class="block text-sm font-medium mb-1">Category</label>
            <select x-model="filters.category" @change="onCategoryChange"
                    class="w-full border-gray-300 rounded-md">
                <option value="">All</option>
                <option value="battery">Batteries</option>
                <option value="panel">Solar Panels</option>
                <option value="connector">Connectors</option>
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Name</label>
            <input type="text" x-model="filters.name" placeholder="Input name"
                   class="w-full border-gray-300 rounded-md"
                   @input.debounce.500ms="fetchProducts">
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Manufacturer</label>
            <input type="text" x-model="filters.manufacturer" placeholder="Input manufacturer"
                   class="w-full border-gray-300 rounded-md"
                   @input.debounce.500ms="fetchProducts">
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Price (min)</label>
            <input type="number" step="0.01" x-model="filters.price_min"
                   class="w-full border-gray-300 rounded-md" @change="fetchProducts">
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Price (max)</label>
            <input type="number" step="0.01" x-model="filters.price_max"
                   class="w-full border-gray-300 rounded-md" @change="fetchProducts">
        </div>

        <div class="md:col-span-4">
            <label class="block text-sm font-medium mb-1">Description</label>
            <input type="text" x-model="filters.description" placeholder="Input description"
                   class="w-full border-gray-300 rounded-md"
                   @input.debounce.700ms="fetchProducts">
        </div>

        <template x-if="filters.category === 'battery'">
            <div class="md:col-span-4 grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Capacity (min, Ah)</label>
                    <input type="number" step="0.1" x-model="filters.capacity_min"
                           class="w-full border-gray-300 rounded-md" @change="fetchProducts">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Capacity (max, Ah)</label>
                    <input type="number" step="0.1" x-model="filters.capacity_max"
                           class="w-full border-gray-300 rounded-md" @change="fetchProducts">
                </div>
            </div>
        </template>
        <template x-if="filters.category === 'panel'">
            <div class="md:col-span-4 grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Power output (min, W)</label>
                    <input type="number" step="0.1" x-model="filters.power_min"
                           class="w-full border-gray-300 rounded-md" @change="fetchProducts">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Power output (max, W)</label>
                    <input type="number" step="0.1" x-model="filters.power_max"
                           class="w-full border-gray-300 rounded-md" @change="fetchProducts">
                </div>
            </div>
        </template>
        <template x-if="filters.category === 'connector'">
            <div class="md:col-span-4">
                <label class="block text-sm font-medium mb-1">Connector Type</label>
                <select x-model="filters.connector_type" @change="fetchProducts"
                        class="w-full border-gray-300 rounded-md">
                    <option value="">All types</option>
                    <template x-for="type in connectorTypes" :key="type">
                        <option :value="type" x-text="type"></option>
                    </template>
                </select>
            </div>
        </template>
    </form>

    <!-- 🧾 Results Table -->
    <div class="mt-8 bg-white shadow rounded-2xl overflow-hidden" x-show="products.length > 0">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-100">
            <tr>
                <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Name</th>
                <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Manufacturer</th>
                <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Category</th>
                <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Price (PLN)</th>
                <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Description</th>
            </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
            <template x-for="p in products" :key="p.id">
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-2 font-medium text-gray-800" x-text="p.name"></td>
                    <td class="px-4 py-2" x-text="p.manufacturer"></td>
                    <td class="px-4 py-2 capitalize" x-text="p.category"></td>
                    <td class="px-4 py-2 text-blue-600 font-semibold" x-text="p.price"></td>
                    <td class="px-4 py-2 text-sm text-gray-600" x-text="p.description"></td>
                </tr>
            </template>
            </tbody>
        </table>
    </div>

    <div class="mt-6 text-center text-gray-500" x-show="products.length === 0">
        No results found for the selected filters.
    </div>
</div>

<script>
    function productCatalog() {
        return {
            products: [],
            filters: {
                category: '',
                name: '',
                manufacturer: '',
                price_min: '',
                price_max: '',
                description: '',
                capacity_min: '',
                capacity_max: '',
                power_min: '',
                power_max: '',
                connector_type: ''
            },
            async fetchProducts() {
                const params = new URLSearchParams();
                for (const [k, v] of Object.entries(this.filters)) {
                    if (v) params.append(k, v);
                }

                const res = await fetch(`/api/products?${params.toString()}`);
                const data = await res.json();

                // Laravel paginator returns { data: [...], meta: {...} }
                this.products = data.data ?? data;
            },
            async fetchConnectorTypes() {
                const res = await fetch('/api/connector-types');
                this.connectorTypes = await res.json();
            },
            async onCategoryChange() {
                const selected = this.filters.category;

                this.filters.capacity_min = '';
                this.filters.capacity_max = '';
                this.filters.power_max = '';
                this.filters.power_mino = '';
                this.filters.connector_type = '';

                if (selected === 'connector') {
                    await this.fetchConnectorTypes();
                }

                await this.fetchProducts();
            },
            async init() {
                await this.fetchConnectorTypes();
                await this.fetchProducts();
            }
        }
    }
</script>

</body>
</html>
