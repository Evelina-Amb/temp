<x-app-layout>

<div class="max-w-6xl mx-auto py-10 px-4">

    {{-- SUCCESS MESSAGE --}}
    @if(session('success'))
        <div class="mb-6 px-4">
            <div class="bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded">
                {{ session('success') }}
            </div>
        </div>
    @endif

    {{-- LISTING CARD --}}
    <div class="bg-white rounded-lg shadow p-6">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">

            {{-- LEFT: IMAGE GALLERY --}}
            <div>
                <img 
                    id="mainImage"
                    src="{{ $listing->ListingPhoto->first()->failo_url ?? 'https://via.placeholder.com/600x450?text=No+Image' }}"
                    class="rounded-lg shadow w-full max-h-[450px] object-cover mb-4"
                >

                @if($listing->ListingPhoto->count() > 1)
                    <div class="flex gap-3">
                        @foreach($listing->ListingPhoto as $photo)
                            <img 
                                src="{{ $photo->failo_url }}"
                                class="w-20 h-20 rounded object-cover cursor-pointer border hover:ring-2 hover:ring-blue-400"
                                onclick="document.getElementById('mainImage').src=this.src"
                            >
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- RIGHT: DETAILS --}}
            <div class="flex flex-col">

                {{-- CATEGORY --}}
                <div class="mb-3">
                    <span class="inline-block bg-blue-100 text-blue-700 px-3 py-1 rounded text-sm">
                        {{ $listing->Category->pavadinimas ?? 'Kategorija' }}
                    </span>
                </div>

                {{-- TITLE + FAVORITE BUTTON --}}
                <div class="flex items-center justify-between mb-4">

                    <h1 class="text-3xl font-bold text-gray-900">
                        {{ $listing->pavadinimas }}
                    </h1>

                    {{-- FAVORITE BUTTON (NOT SHOWN ON YOUR OWN LISTINGS) --}}
                    @if(auth()->check() && auth()->id() !== $listing->user_id)
                    <div x-data="{ favorites: Alpine.store('favorites').list }">

                        <button
                            @click="
                                Alpine.store('favorites').toggle({{ $listing->id }});
                                favorites = Alpine.store('favorites').list;
                            "
                            class="text-3xl"
                        >
                            {{-- Filled Heart --}}
                            <span 
                                x-show="favorites.includes({{ $listing->id }})"
                                class="text-red-500"
                            >❤️</span>

                            {{-- Empty Heart --}}
                            <span 
                                x-show="!favorites.includes({{ $listing->id }})"
                                class="text-gray-300"
                            >🤍</span>
                        </button>

                    </div>
                    @endif

                </div>

                {{-- DESCRIPTION --}}
                <div class="text-gray-700 leading-relaxed mb-6 whitespace-pre-line">
                    {!! nl2br(e($listing->aprasymas)) !!}
                </div>

                {{-- PRICE --}}
                <div class="text-2xl font-semibold text-gray-800 mb-2">
                    {{ number_format($listing->kaina, 2, ',', '.') }} €
                    <span class="text-gray-500 text-sm">
                        @if($listing->tipas === 'preke') / vnt @else / Service @endif
                    </span>
                </div>

                {{-- AVAILABLE QUANTITY --}}
                <div class="text-gray-700 mb-4">
                    <strong>Available:</strong> 
                    <span class="{{ $listing->kiekis == 0 ? 'text-red-600 font-bold' : '' }}">
                        {{ $listing->kiekis }}
                    </span>
                </div>

                {{-- RENEWABLE BADGE --}}
                @if($listing->is_renewable)
                    <div class="mb-4">
                        <span class="inline-block bg-green-100 text-green-700 px-3 py-1 rounded text-sm">
                            Renewable product – seller restocks this item
                        </span>
                    </div>

                    @if($listing->kiekis == 0)
                        <div class="text-yellow-600 font-semibold mb-4">
                            This item is currently out of stock, but the seller will restock it.
                        </div>
                    @endif
                @endif

                {{-- ADD TO CART OR EDIT BUTTON --}}
                @if(auth()->check() && auth()->id() === $listing->user_id)

                    {{-- OWNER VIEW: SHOW EDIT INSTEAD OF CART --}}
                    <a href="{{ route('listing.edit', $listing->id) }}" 
                        class="px-6 py-3 bg-blue-600 text-white rounded hover:bg-blue-700 transition text-center w-40">
                        Edit listing
                    </a>

                @else

                    {{-- NORMAL USER: ADD TO CART --}}
                    <form method="POST" action="{{ route('cart.add', $listing->id) }}" class="flex items-center gap-4">
                        @csrf

                        {{-- Quantity selector --}}
                        <div class="flex items-center border rounded">
                            <button 
                                type="button"
                                onclick="
                                    let q=this.nextElementSibling;
                                    let val=parseInt(q.value)||1;
                                    q.value = Math.max(1, val - 1);
                                "
                                class="px-3 py-2 hover:bg-gray-100"
                            >-</button>

                            <input 
                                type="number" 
                                name="quantity"
                                value="1"
                                min="1"
                                max="{{ $listing->kiekis }}"
                                class="w-16 text-center border-l border-r"
                                {{ $listing->kiekis == 0 ? 'disabled' : '' }}
                            >

                            <button 
                                type="button"
                                onclick="
                                    let q=this.previousElementSibling;
                                    let val=parseInt(q.value)||1;
                                    if (val < {{ $listing->kiekis }}) q.value = val + 1;
                                "
                                class="px-3 py-2 hover:bg-gray-100"
                            >+</button>
                        </div>

                        <button 
                            type="submit"
                            class="px-6 py-3 bg-blue-600 text-white rounded hover:bg-blue-700 transition"
                            {{ $listing->kiekis == 0 ? 'disabled' : '' }}
                        >
                            Add to cart
                        </button>
                    </form>

                    @if($listing->kiekis == 0)
                        <div class="mt-2 text-red-600 font-semibold">
                            This item is currently out of stock.
                        </div>
                    @endif

                @endif

                {{-- SELLER INFO --}}
                <div class="mt-10 border-t pt-6">
                    <h3 class="font-semibold text-gray-800 mb-2">Seller</h3>

                    <div class="bg-gray-50 p-4 rounded border">
                        <div class="text-gray-900 font-semibold text-lg">
                            {{ $listing->user->vardas }} {{ $listing->user->pavarde }}
                        </div>

                        <div class="text-gray-600 text-sm mt-1">
                            {{ $listing->user->el_pastas }}
                        </div>

                        @if($listing->user->telefonas)
                            <div class="text-gray-700 text-sm mt-1">
                                Tel: {{ $listing->user->telefonas }}
                            </div>
                        @endif
                    </div>
                </div>

            </div>

        </div>

    </div>

    {{-- SIMILAR PRODUCTS --}}
    @if($similar->count() > 0)
        <div class="mt-14">

            <h2 class="text-2xl font-bold mb-6">
                Other products from this seller {{ $listing->user->vardas }} {{ $listing->user->pavarde }}
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">

                @foreach($similar as $s)
                    @if($s->id !== $listing->id)
                        <a href="{{ route('listing.single', $s->id) }}" 
                           class="bg-white shadow rounded overflow-hidden hover:shadow-md transition">

                            <img 
                                src="{{ $s->ListingPhoto->first()->failo_url ?? 'https://via.placeholder.com/300' }}"
                                class="w-full h-40 object-cover"
                            >

                            <div class="p-4">
                                <div class="font-semibold mb-1">{{ $s->pavadinimas }}</div>
                                <div class="text-green-700 font-semibold">
                                    {{ number_format($s->kaina, 2, ',', '.') }} €
                                </div>
                            </div>
                        </a>
                    @endif
                @endforeach

            </div>
        </div>
    @endif

</div>

</x-app-layout>
