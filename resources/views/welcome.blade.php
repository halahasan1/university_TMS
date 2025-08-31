<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Latakia University · TMS</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <!-- Tailwind CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            brand: {
              50:  '#fff8e7',
              100: '#fdecc8',
              200: '#f9d996',
              300: '#f4c463',
              400: '#efb237',
              500: '#e59e0d',   /* primary */
              600: '#c8840a',
              700: '#a86d08',
              800: '#7c5206',
              900: '#4c3203',
            },
          },
          boxShadow: {
            'soft': '0 10px 40px -10px rgba(17,24,39,.18)',
          },
        },
      },
    }
  </script>

  <!-- Inter font (nice, modern) -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <style>html,body{font-family:Inter,system-ui,-apple-system,Segoe UI,Roboto,Ubuntu,Cantarell,'Helvetica Neue',Arial}</style>

  <!-- Lottie -->
  <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
</head>

<body class="bg-white text-gray-800 antialiased">
  <!-- Decorative background blobs -->
  <div class="pointer-events-none fixed inset-0 -z-10">
    <div class="absolute -top-24 -left-24 h-72 w-72 rounded-full bg-brand-100 blur-3xl opacity-70"></div>
    <div class="absolute -bottom-24 -right-24 h-72 w-72 rounded-full bg-amber-200 blur-3xl opacity-70"></div>
  </div>

  <!-- Header -->
  <header class="sticky top-0 z-50 bg-white/80 backdrop-blur supports-[backdrop-filter]:bg-white/60 border-b border-gray-100">
    <div class="mx-auto max-w-7xl px-6 py-4">
      <div class="flex items-center justify-between">
        <!-- Brand -->
        <a href="/" class="group inline-flex items-center gap-3">
          <img
            src="/images/latakia-university-logo.png"
            alt="Latakia University"
            class="h-10 w-10 rounded-full ring-1 ring-brand-500/30 bg-white object-cover shadow-sm transition duration-300 group-hover:scale-105 group-hover:shadow"
          />
          <div class="leading-none">
            <span class="block text-[13px] uppercase tracking-widest text-gray-500">Latakia University</span>
            <span class="block text-xl font-extrabold tracking-tight">
              <span class="text-gray-900">T</span><span class="bg-gradient-to-r from-brand-500 to-amber-400 bg-clip-text text-transparent">MS</span>
            </span>
          </div>
        </a>

        <!-- Actions -->
        <div class="flex items-center gap-3">
          <a href="adminPanel/login"
             class="inline-flex items-center justify-center rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm transition hover:-translate-y-0.5 hover:shadow-soft">
            Sign in
          </a>
          <a href="adminPanel/register"
             class="inline-flex items-center justify-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:-translate-y-0.5 hover:bg-brand-600 hover:shadow-soft">
            Create account
          </a>
        </div>
      </div>
    </div>
  </header>

  <!-- Hero -->
  <section class="relative">
    <div class="mx-auto max-w-7xl px-6 py-16 sm:py-20">
      <div class="grid items-center gap-12 md:grid-cols-2">
        <div>
          <div class="inline-flex items-center gap-2 rounded-full border border-amber-200 bg-amber-50 px-3 py-1 text-xs font-medium text-amber-700">
            <span class="h-2 w-2 rounded-full bg-amber-500"></span> Smart University Platform
          </div>
          <h1 class="mt-5 text-4xl font-extrabold tracking-tight text-gray-900 sm:text-5xl">
            A modern, elegant <span class="bg-gradient-to-r from-brand-500 to-amber-400 bg-clip-text text-transparent">Task & News</span> system
          </h1>
          <p class="mt-5 text-lg text-gray-600">
            Track tasks, publish faculty news, and receive instant notifications—all in one refined dashboard.
          </p>

          <div class="mt-8 flex flex-wrap items-center gap-3">
            <a href="adminPanel/register"
               class="inline-flex items-center justify-center rounded-xl bg-gray-900 px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-soft">
              Get started
            </a>
            <a href="adminPanel/login"
               class="inline-flex items-center justify-center rounded-xl border border-gray-200 bg-white px-6 py-3 text-sm font-semibold text-gray-700 shadow-sm transition hover:-translate-y-0.5 hover:shadow-soft">
              I already have an account
            </a>
          </div>

          <!-- Trust badges -->
          <div class="mt-8 flex items-center gap-6 text-sm text-gray-500">
            <div class="flex items-center gap-2">
              <svg class="h-5 w-5 text-brand-500" fill="currentColor" viewBox="0 0 20 20"><path d="M2.166 10.083a.75.75 0 011.06 0l3.11 3.11 7.438-7.438a.75.75 0 111.06 1.06l-8 8a.75.75 0 01-1.06 0l-3.64-3.64a.75.75 0 010-1.06z"/></svg>
              Secure & fast
            </div>
            <div class="flex items-center gap-2">
              <svg class="h-5 w-5 text-brand-500" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a6 6 0 00-6 6v2.586l-.793.793A1 1 0 004 13h12a1 1 0 00.707-1.707L16 10.586V8a6 6 0 00-6-6zM7 13a3 3 0 006 0H7z"/></svg>
              Real-time notifications
            </div>
          </div>
        </div>

        <div class="relative mx-auto flex justify-center">
          <div class="absolute -inset-4 -z-10 rounded-[2rem] bg-gradient-to-tr from-amber-200 via-amber-100 to-white blur-2xl"></div>
          <div class="rounded-3xl border border-amber-100 bg-white/80 p-3 shadow-soft backdrop-blur">
            <lottie-player
              src="https://assets5.lottiefiles.com/packages/lf20_puciaact.json"
              background="transparent" speed="1"
              style="width: 420px; height: 420px;"
              loop autoplay>
            </lottie-player>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Features -->
  <section class="bg-white">
    <div class="mx-auto max-w-7xl px-6 py-16">
      <h2 class="text-center text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">
        Everything you need to run your Faculty
      </h2>
      <p class="mx-auto mt-3 max-w-2xl text-center text-gray-600">
        Thoughtful tools with a refined look & smooth interactions.
      </p>

      <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Card -->
        <div class="group rounded-2xl border border-gray-100 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-soft">
          <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-brand-50 text-brand-600 shadow-sm group-hover:scale-110 transition">
            📢
          </div>
          <h3 class="mt-5 text-lg font-semibold text-gray-900">University News</h3>
          <p class="mt-2 text-sm text-gray-600">Publish and browse official announcements in a beautiful feed.</p>
        </div>

        <div class="group rounded-2xl border border-gray-100 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-soft">
          <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-brand-50 text-brand-600 shadow-sm group-hover:scale-110 transition">
            ✅
          </div>
          <h3 class="mt-5 text-lg font-semibold text-gray-900">Task Management</h3>
          <p class="mt-2 text-sm text-gray-600">Assign tasks with priorities, due dates, and subtasks.</p>
        </div>

        <div class="group rounded-2xl border border-gray-100 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-soft">
          <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-brand-50 text-brand-600 shadow-sm group-hover:scale-110 transition">
            🔔
          </div>
          <h3 class="mt-5 text-lg font-semibold text-gray-900">Notifications</h3>
          <p class="mt-2 text-sm text-gray-600">Get instant alerts on likes, comments, and new assignments.</p>
        </div>

        <div class="group rounded-2xl border border-gray-100 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-soft">
          <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-brand-50 text-brand-600 shadow-sm group-hover:scale-110 transition">
            👥
          </div>
          <h3 class="mt-5 text-lg font-semibold text-gray-900">User Roles</h3>
          <p class="mt-2 text-sm text-gray-600">Super Admin, Dean, Professors & Students with tailored access.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- CTA -->
  <section class="relative overflow-hidden">
    <div class="mx-auto max-w-7xl px-6 pb-20">
      <div class="relative rounded-3xl border border-amber-100 bg-gradient-to-r from-amber-50 via-white to-amber-100 p-10 shadow-soft">
        <div class="absolute -right-8 -top-8 h-40 w-40 rounded-full bg-amber-200 blur-2xl opacity-60"></div>
        <div class="absolute -left-8 -bottom-8 h-40 w-40 rounded-full bg-amber-100 blur-2xl opacity-60"></div>

        <div class="flex flex-col items-center gap-6 text-center sm:flex-row sm:justify-between sm:text-left">
          <div>
            <h3 class="text-2xl font-bold text-gray-900">Ready to join?</h3>
            <p class="mt-1 text-gray-600">Create your account and start in less than a minute.</p>
          </div>
          <div class="flex items-center gap-3">
            <a href="adminPanel/register"
               class="inline-flex items-center justify-center rounded-xl bg-brand-500 px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:-translate-y-0.5 hover:bg-brand-600 hover:shadow-soft">
              Sign up now
            </a>
            <a href="adminPanel/login"
               class="inline-flex items-center justify-center rounded-xl border border-gray-200 bg-white px-6 py-3 text-sm font-semibold text-gray-700 shadow-sm transition hover:-translate-y-0.5 hover:shadow-soft">
              Sign in
            </a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer class="border-t border-gray-100 bg-white">
    <div class="mx-auto max-w-7xl px-6 py-8 text-sm text-gray-500">
      © 2025 Latakia University · TMS — All rights reserved.
    </div>
  </footer>
</body>
</html>
