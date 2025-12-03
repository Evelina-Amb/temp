<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Country;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
public function edit(Request $request)
{
    $user = $request->user()->load('Address.City');

    return view('profile.edit', [
        'user'      => $user,
        'countries' => \App\Models\Country::select('id', 'pavadinimas')
                        ->orderBy('pavadinimas')
                        ->get(),
        'cities'    => \App\Models\City::select('id', 'pavadinimas', 'country_id')
                        ->orderBy('pavadinimas')
                        ->get(),
    ]);
}

    /**
     * Update the user's profile information.
     */
    public function update(Request $request)
    {
        $user = auth()->user();

        // VALIDATION
        $validated = $request->validate([
            'vardas'     => ['nullable', 'string', 'max:255'],
            'pavarde'    => ['nullable', 'string', 'max:255'],
            'el_pastas'  => ['required', 'email', 'max:255'],
            'telefonas'  => ['nullable', 'string', 'max:50'],

            // Address fields
            'gatve'      => ['nullable', 'string', 'max:255'],
            'namo_nr'    => ['nullable', 'string', 'max:50'],
            'buto_nr'    => ['nullable', 'string', 'max:50'],
            'city_id'    => ['nullable', 'exists:city,id'],

            'role'       => ['nullable', 'string'],
        ]);

        // UPDATE USER MAIN FIELDS
        $user->update([
            'vardas'     => $validated['vardas'] ?? $user->vardas,
            'pavarde'    => $validated['pavarde'] ?? $user->pavarde,
            'el_pastas'  => $validated['el_pastas'] ?? $user->el_pastas,
            'telefonas'  => $validated['telefonas'] ?? $user->telefonas,
            'role'       => $request->has('role') ? 'seller' : 'buyer',
        ]);

        /**
         * HANDLE ADDRESS
         */
        $hasAddressData =
            !empty($validated['gatve']) ||
            !empty($validated['namo_nr']) ||
            !empty($validated['buto_nr']) ||
            !empty($validated['city_id']);

        if ($hasAddressData) {

            // Update existing address
            if ($user->address_id && $user->address) {
                $address = $user->address;

                $address->gatve     = $validated['gatve']    ?? $address->gatve;
                $address->namo_nr   = $validated['namo_nr']  ?? $address->namo_nr;
                $address->buto_nr   = $validated['buto_nr']  ?? $address->buto_nr;
                $address->city_id   = $validated['city_id']  ?? $address->city_id;

                $address->save();

            } else {

                // Create new address
                $address = Address::create([
                    'gatve'     => $validated['gatve'] ?? '',
                    'namo_nr'   => $validated['namo_nr'] ?? '',
                    'buto_nr'   => $validated['buto_nr'] ?? null,
                    'city_id'   => $validated['city_id'] ?? null,
                ]);

                $user->address_id = $address->id;
                $user->save();
            }
        }

        return back()->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    public function updatePassword(Request $request)
{
    $request->validate([
        'current_password' => ['required', 'current_password'],
        'password' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],
    ]);

    $user = $request->user();

    $user->update([
        'slaptazodis' => $request->password,
    ]);

    return back()->with('status', 'password-updated');
}

}
