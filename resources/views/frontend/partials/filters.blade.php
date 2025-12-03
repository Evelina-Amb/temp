<form method="GET" action="{{ route('search.listings') }}" class="grid grid-cols-1 sm:grid-cols-5 gap-4">

    <input type="hidden" name="q" value="{{ request('q') }}">

    <!-- Category -->
    <select name="category_id" class="border rounded px-3 py-2">
        <option value="">Category</option>
        @foreach(\App\Models\Category::all() as $cat)
            <option value="{{ $cat->id }}" @selected(request('category_id') == $cat->id)>
                {{ $cat->pavadinimas }}
            </option>
        @endforeach
    </select>

    <!-- Type -->
    <select name="tipas" class="border rounded px-3 py-2">
        <option value="">Type</option>
        <option value="preke" @selected(request('tipas') == 'preke')>Product</option>
        <option value="paslauga" @selected(request('tipas') == 'paslauga')>Service</option>
    </select>

    <!-- Min Price -->
    <input 
        type="number" 
        name="min_price" 
        class="border rounded px-3 py-2"
        placeholder="Min price"
        value="{{ request('min_price') }}"
    >

    <!-- Max Price -->
    <input 
        type="number" 
        name="max_price" 
        class="border rounded px-3 py-2"
        placeholder="Max price"
        value="{{ request('max_price') }}"
    >

    <!-- Sort -->
    <select name="sort" class="border rounded px-3 py-2">
        <option value="">Sort by</option>
        <option value="newest"     @selected(request('sort') == 'newest')>Newest first</option>
        <option value="oldest"     @selected(request('sort') == 'oldest')>Oldest first</option>
        <option value="price_asc"  @selected(request('sort') == 'price_asc')>Price: Low to High</option>
        <option value="price_desc" @selected(request('sort') == 'price_desc')>Price: High to Low</option>
    </select>

    <!-- Submit -->
    <button class="bg-blue-600 text-white px-4 py-2 rounded col-span-full w-32">
        Apply
    </button>

</form>
