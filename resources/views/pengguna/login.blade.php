<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Login</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Custom Tailwind Colors -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#007bff',
                        secondary: '#00c6ff',
                        hoverBlue: '#0056b3'
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-300">

    <div class="min-h-screen flex items-center justify-center">
        <div class="w-full max-w-md bg-white rounded-[15px] shadow-[0_5px_25px_rgba(0,0,0,0.1)] mt-20">
            <div class="bg-gradient-to-r from-primary to-secondary text-white text-center text-lg font-bold rounded-t-[15px] py-4">
                Login
            </div>
            <div class="p-6">

                {{-- Error alert for login failure --}}
                @if ($errors->has('login'))
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                        {{ $errors->first('login') }}
                    </div>
                @endif

                {{-- Optional: Success message (e.g. after logout) --}}
                @if(session('success'))
                    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                        {{ session('success') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login.post') }}" autocomplete="off">
                    @csrf

                    <!-- Email -->
                    <div class="mb-4">
                        <label for="email" class="block text-gray-700 font-medium mb-1">Email Address</label>
                        <input id="email" type="email"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-primary @error('email') border-red-500 @enderror"
                               name="email"
                               value="{{ old('email') }}"
                               required autocomplete="email" autofocus>
                        @error('email')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="mb-4">
                        <label for="password" class="block text-gray-700 font-medium mb-1">Password</label>
                        <input id="password" type="password"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-primary @error('password') border-red-500 @enderror"
                               name="password"
                               required autocomplete="current-password">
                        @error('password')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Remember Me -->
                    <div class="mb-4 flex items-center">
                        <input class="mr-2 rounded border-gray-300" type="checkbox" name="remember" id="remember"
                               {{ old('remember') ? 'checked' : '' }}>
                        <label class="text-sm text-gray-700" for="remember">
                            Remember Me
                        </label>
                    </div>

                    <!-- Submit -->
                    <div>
                        <button type="submit"
                                class="w-full bg-primary hover:bg-hoverBlue text-white py-2 px-4 rounded-md transition duration-200">
                            Login
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</body>
</html>
