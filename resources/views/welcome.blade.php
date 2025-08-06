<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome to University TMS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Lottie Player -->
    <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>

</head>
<body class="bg-white text-gray-800">

    <!-- Header -->
    <header class="flex justify-between items-center px-10 py-6 bg-amber-100 shadow">
        <h1 class="text-2xl font-bold text-amber-700">University TMS</h1>
        <div class="space-x-4">
            <a href="adminPanel/login" class="px-4 py-2 bg-amber-600 text-white rounded hover:bg-amber-700">تسجيل الدخول</a>
            <a href="adminPanel/register" class="px-4 py-2 border border-amber-600 text-amber-700 rounded hover:bg-amber-50">التسجيل</a>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="flex flex-col md:flex-row items-center justify-between px-10 py-16 bg-gradient-to-r from-yellow-50 to-amber-100">
        <div class="md:w-1/2 mb-10 md:mb-0">
            <h2 class="text-4xl font-extrabold mb-6 text-amber-800 leading-snug">نظام إدارة جامعي متكامل</h2>
            <p class="text-lg text-gray-600 mb-6">تابع مهامك، اشعاراتك، وسجلاتك الأكاديمية بكل سهولة من خلال لوحة تحكم ذكية وسهلة الاستخدام.</p>
            <a href="adminPanel/register" class="bg-amber-600 text-white px-6 py-3 rounded hover:bg-amber-700">ابدأ الآن</a>
        </div>
        <div class="md:w-1/2 flex justify-center">
            <lottie-player
                src="https://assets5.lottiefiles.com/packages/lf20_puciaact.json"
                background="transparent"
                speed="1"
                style="width: 400px; height: 400px;"
                loop autoplay>
            </lottie-player>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-20 px-10 bg-white">
        <h3 class="text-3xl font-bold text-center mb-12 text-amber-800">مميزات النظام</h3>
        <div class="grid md:grid-cols-4 gap-8 text-center">
            <div>
                <div class="text-amber-600 text-5xl mb-4">📢</div>
                <h4 class="text-xl font-semibold mb-2">الأخبار</h4>
                <p class="text-gray-600">اطّلع على كل جديد من أخبار الكلية والإعلانات الرسمية.</p>
            </div>
            <div>
                <div class="text-amber-600 text-5xl mb-4">📝</div>
                <h4 class="text-xl font-semibold mb-2">المهام</h4>
                <p class="text-gray-600">نظّم مهامك وكن على اطلاع دائم بمواعيدك.</p>
            </div>
            <div>
                <div class="text-amber-600 text-5xl mb-4">🔔</div>
                <h4 class="text-xl font-semibold mb-2">الإشعارات</h4>
                <p class="text-gray-600">استقبل التنبيهات فورياً لكل ما يخصك.</p>
            </div>
            <div>
                <div class="text-amber-600 text-5xl mb-4">👥</div>
                <h4 class="text-xl font-semibold mb-2">إدارة المستخدمين</h4>
                <p class="text-gray-600">لوحة تحكم مرنة لإدارة الطلاب، الموظفين، والمدرسين.</p>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="py-16 px-10 bg-amber-50 text-center">
        <h3 class="text-2xl font-bold text-amber-800 mb-4">جاهز للانضمام؟</h3>
        <p class="text-gray-700 mb-6">ابدأ الآن وكن جزءاً من التجربة التعليمية الذكية.</p>
        <a href="adminPanel/register" class="bg-amber-600 text-white px-6 py-3 rounded hover:bg-amber-700">سجّل حسابك الآن</a>
    </section>

    <!-- Footer -->
    <footer class="bg-amber-100 py-6 text-center text-sm text-gray-600">
        &copy; 2025 - جميع الحقوق محفوظة - University TMS
    </footer>

</body>
</html>
