<x-guest-layout>
    <div class="w-full max-w-md px-6 py-8
                bg-white/10 backdrop-blur-xl
                rounded-2xl shadow-2xl
                text-white">

        <h2 class="text-2xl font-bold text-center mb-6">
           Rivo Welcome Back
        </h2>

        <x-auth-session-status class="mb-4 text-green-300"
            :status="session('status')" />

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Email -->
            <div class="mb-4">
                <x-input-label for="email" value="Email"
                    class="text-white mb-1" />

                <x-text-input
                    id="email"
                    type="email"
                    name="email"
                    :value="old('email')"
                    required autofocus
                    class="w-full bg-white/20 text-white
                           border-0 rounded-lg
                           focus:ring-2 focus:ring-purple-400"
                />

                <x-input-error :messages="$errors->get('email')"
                    class="mt-1 text-red-300" />
            </div>

            <!-- Password -->
            <div class="mb-4">
                <x-input-label for="password" value="Password"
                    class="text-white mb-1" />

                <x-text-input
                    id="password"
                    type="password"
                    name="password"
                    required
                    class="w-full bg-white/20 text-white
                           border-0 rounded-lg
                           focus:ring-2 focus:ring-purple-400"
                />

                <x-input-error :messages="$errors->get('password')"
                    class="mt-1 text-red-300" />
            </div>

            <!-- Remember -->
            <div class="flex items-center mb-4 text-sm">
                <input id="remember_me" type="checkbox"
                       class="rounded border-none text-purple-500 focus:ring-purple-400"
                       name="remember">

                <label for="remember_me" class="ms-2">
                    Remember me
                </label>
            </div>

<button
  class="w-full py-2 rounded-xl
         bg-gradient-to-r from-[#3f4526] to-[#515831]
         text-[#ddcdbc]
         hover:scale-[1.02] transition
         font-semibold shadow-xl">
  Log in
</button>



            @if (Route::has('password.request'))
                <div class="text-center mt-4">
                    <a class="text-sm text-purple-300 hover:underline"
                       href="{{ route('password.request') }}">
                        Forgot your password?
                    </a>
                </div>
            @endif
        </form>
    </div>

</x-guest-layout>
