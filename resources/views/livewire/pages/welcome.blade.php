<?php

use function Livewire\Volt\layout;
use function Livewire\Volt\state;

layout('livewire.components.layouts.guest');

$menu = [
    [
        'title' => 'تحليل المبيعات',
        'description' => 'تحليلات شاملة لمبيعاتك اليومية والشهرية مع تقارير مفصلة',
        'image' => '/assets/images/dashboard.png',
    ],
    [
        'title' => 'إدارة المخزون',
        'description' => 'تتبع مخزونك بسهولة مع تنبيهات تلقائية عند انخفاض الكميات',
        'image' => '/assets/images/stock-management.png',
    ],
    [
        'title' => 'نظام الدفع السريع',
        'description' => 'معالجة المعاملات بسرعة مع حاسبة ذكية ودعم متعدد لطرق الدفع',
        'image' => '/assets/images/calculator-payment.png',
    ],
];

$prices = [
    [
        'title' => 'الأساسية',
        'description' => 'مثالية للشركات الصغيرة',
        'price' => '$29',
        'button' => 'ابدأ الآن',
        'route' => 'auth.register',
        'includes' => [
            '100 منتج',
            'مستخدمين 2',
            '1 جيجابايت تخزين',
            'تقارير أساسية',
            'دعم عبر البريد الإلكتروني',
        ],
        'excludes' => [
            'التحليلات المتقدمة',
            'تقارير مخصصة',
            'دعم أولوية',
        ],
    ],
    [
        'title' => 'الاحترافية',
        'description' => 'للشركات المتنامية',
        'price' => '$99',
        'button' => 'ابدأ الآن',
        'route' => 'auth.register',
        'includes' => [
            '1,000 منتج',
            '10 مستخدمين',
            '10 جيجابايت تخزين',
            'تقارير متقدمة',
            'التحليلات المتقدمة',
            'دعم أولوية',
        ],
        'excludes' => [
            'علامة بيضاء',
        ],
    ],
    [
        'title' => 'المؤسسات',
        'description' => 'لا حدود للموارد',
        'price' => '$299',
        'button' => 'ابدأ الآن',
        'route' => 'auth.register',
        'includes' => [
            'منتجات غير محدودة',
            'مستخدمين غير محدودين',
            'تخزين غير محدود',
            'جميع الميزات',
            'علامة بيضاء',
            'دعم مخصص',
            'تكاملات مخصصة',
        ],
        'excludes' => [],
    ],
];

$mainFeatures = [
    [
        'title' => 'متعدد المستأجرين',
        'description' => 'نظام SaaS كامل مع عزل قاعدة البيانات لكل عميل',
        'icon' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-7 h-7">
              <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
            </svg>',
    ],
    [
        'title' => 'اشتراكات مرنة',
        'description' => 'ثلاث خطط اشتراك مع الفوترة عبر Stripe',
        'icon' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-7 h-7">
              <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z" />
            </svg>',
    ],
    [
        'title' => 'متعدد المنصات',
        'description' => 'يعمل على الويب والأندرويد مع مزامنة فورية',
        'icon' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-7 h-7">
              <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 0 0 6 3.75v16.5a2.25 2.25 0 0 0 2.25 2.25h7.5A2.25 2.25 0 0 0 18 20.25V3.75a2.25 2.25 0 0 0-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" />
            </svg>',
    ],
    [
        'title' => 'تتبع الاستخدام',
        'description' => 'مراقبة المنتجات والمستخدمين والتخزين في الوقت الفعلي',
        'icon' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-7 h-7">
              <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" />
            </svg>',
    ],
];

state([
    'menu' => $menu,
    'prices' => $prices,
    'mainFeatures' => $mainFeatures,
]);

?>

<div>
  <section class="w-full bg-white lg:h-screen md:h-[80vh]" data-tails-scripts="//unpkg.com/alpinejs">
    <header class="relative block w-full py-6 leading-10 text-center">
      <div class="w-full px-6 mx-auto leading-10 text-center lg:px-8 max-w-7xl">
        <div class="box-border flex flex-wrap items-center justify-between -mx-4 text-indigo-900">
          <div class="relative z-10 flex items-center w-auto px-4 leading-10 lg:flex-grow-0 lg:flex-shrink-0 lg:text-left">
            <a href="/" class="flex box-border font-sans text-2xl font-bold text-left text-gray-900 no-underline bg-transparent cursor-pointer focus:no-underline items-end gap-x-2">
              <img src="{{ env('APP_URL') }}/assets/logo/image.png" class="h-10"> <p>كاشير</p>
            </a>
          </div>

          <div class="relative items-center hidden px-4 mt-2 space-x-5 font-medium leading-10 md:flex md:flex-grow-0 md:flex-shrink-0 md:mt-0 md:text-right lg:flex-grow-0 lg:flex-shrink-0">
            <a href="{{ route('auth.register') }}" class="bg-lakasir-primary text-white md:w-auto w-full px-8 py-3 rounded-full flex items-center justify-center font-medium text-lg focus:ring-offset-2 focus:ring-2 focus:ring-lakasir-primary">سجل الآن</a>
          </div>

          <!-- Sidebar -->
          <div class="md:hidden">
            <div>
              <div>
                <button class="text-white hidden left-10 fixed top-20 text-2xl px-3 py-1 bg-lakasir-primary rounded-lg"
                  x-ref="xButton"
                  x-on:click="$refs.menu.classList.toggle('hidden'); $refs.xButton.classList.toggle('hidden');"
                  >X</button>
              </div>
              <div class="fixed inset-y-0 right-0 w-64 bg-gray-800 text-white flex-col justify-between z-20 hidden" x-ref="menu">
                <!-- Links -->
                <div class="flex flex-col mt-8">
                  <a href="{{ route('auth.register') }}" class="px-6 py-3 text-sm font-medium">سجل الآن</a>
                </div>
              </div>
            </div>
            <!-- Content -->
            <div class="flex items-center justify-center h-full mr-5 text-gray-800" x-on:click="$refs.menu.classList.toggle('hidden'); $refs.xButton.classList.toggle('hidden');">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"></path></svg>
            </div>
          </div>
        </div>
      </div>
    </header>
    <div x-ref="overlay" class="fixed hidden z-40 w-screen h-screen inset-0 bg-gray-900 bg-opacity-60"></div>
    <!-- The dialog -->
    <div x-ref="dialog"
      class="hidden fixed z-50 top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-auto bg-white rounded-md px-8 py-6 space-y-5 drop-shadow-lg">
      <!--- right Close button -->
      <div class="w-full flex justify-end cursor-pointer" x-on:click="$refs.overlay.classList.add('hidden'); $refs.dialog.classList.add('hidden');">
        <button class="text-gray-600">X</button>
      </div>
      <iframe width="1024" height="576"
        src="https://www.youtube.com/embed/O5rYsoAZ_sk?si=kPhn1AxNqTgQR3Sf&amp;controls=0" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
    </div>


    <main class="w-full relative">
      <div class="max-w-7xl px-10 mx-auto flex lg:flex-row flex-col py-20">
        <div class="w-full lg:w-1/2 flex lg:justify-start justify-start md:justify-center hero-title">
          <div class="lg:py-24  lg:text-left text-left md:text-center">
            <br/>
            <h1 class="mt-4 text-4xl tracking-tight font-extrabold text-gray-800 sm:mt-5 lg:text-left text-left md:text-center sm:text-6xl lg:mt-6 xl:text-7xl">
              <span class="block">كاشير</span>
              <span class="text-lakasir-primary flex items-center justify-start lg:justify-start md:justify-center w-full">نظام نقاط البيع السحابي</span>
            </h1>
            <p class="mt-3 text-base text-gray-400 sm:mt-5 sm:text-xl lg:text-lg  lg:text-left text-left md:text-center xl:text-xl">
              كاشير هو نظام نقاط بيع سحابي متقدم يساعدك على إدارة عملك بكفاءة.
              <br class="xl:block hidden"> ابدأ تجربتك المجانية لمدة 14 يومًا اليوم!
            </p>
            <div class="mt-6 sm:mt-8">
              <div class="flex md:flex-row flex-col md:space-x-5 md:space-y-0 space-y-5 lg:justify-start justify-center">
                <a href="{{ route('auth.register') }}" class="bg-lakasir-primary text-white md:w-auto w-full px-8 py-4 rounded-full flex items-center justify-center font-medium text-lg focus:ring-offset-2 focus:ring-2 focus:ring-lakasir-primary">ابدأ تجربتك المجانية</a>
                <a href="#price"
                  class="bg-gray-800 text-white px-8 py-4 rounded-full flex items-center justify-center font-medium text-lg focus:ring-offset-2 focus:ring-2 focus:ring-gray-800"
                >
                  <span>عرض الأسعار</span>
                </a>
              </div>
            </div>
          </div>
        </div>
        <div class="w-full lg:w-1/2 lg:flex lg:justify-center lg:max-w-none max-w-md lg:mt-0 mt-20 mx-auto relative hidden hero-phone">
          <div class="w-auto sm:w-64 absolute bottom-0 transform md:bottom-auto md:top-1/2 p-8 translate-y-16 md:translate-y-24 md:ml-16 z-10 left-0 bg-white text-gray-400 rounded-xl shadow-2xl hidden md:block">
            <div class="inline-flex absolute top-0 transform -translate-y-full bg-lakasir-primary left-0  space-x-1 px-4 items-center h-9 w-auto rounded-full -mt-2">

              <svg class="w-4 h-4 text-white fill-current" viewBox="0 0 534 509" xmlns="http://www.w3.org/2000/svg"><path d="m409.8 313.24 114.8-94.637c16.238-13.441 7.84-39.762-13.441-40.879l-147.84-8.96c-8.96-.56-16.801-6.161-20.16-14.56l-54.32-138.88c-7.84-19.602-35.281-19.602-43.121 0l-54.32 138.32c-3.36 8.399-11.199 14-20.16 14.56l-148.4 8.96c-21.281 1.121-29.68 27.441-13.441 40.879l114.8 94.078c6.719 5.602 10.078 15.121 7.84 23.52l-37.52 143.92c-5.04 20.16 16.8 36.398 34.719 25.199l124.88-80.078c7.84-5.04 17.359-5.04 24.64 0l125.44 80.078c17.923 11.199 39.763-5.04 34.72-25.199l-37.52-143.36c-1.68-8.398 1.12-17.359 8.402-22.961h.002Z" fill-rule="nonzero"/></svg>
              <svg class="w-4 h-4 text-white fill-current" viewBox="0 0 534 509" xmlns="http://www.w3.org/2000/svg"><path d="m409.8 313.24 114.8-94.637c16.238-13.441 7.84-39.762-13.441-40.879l-147.84-8.96c-8.96-.56-16.801-6.161-20.16-14.56l-54.32-138.88c-7.84-19.602-35.281-19.602-43.121 0l-54.32 138.32c-3.36 8.399-11.199 14-20.16 14.56l-148.4 8.96c-21.281 1.121-29.68 27.441-13.441 40.879l114.8 94.078c6.719 5.602 10.078 15.121 7.84 23.52l-37.52 143.92c-5.04 20.16 16.8 36.398 34.719 25.199l124.88-80.078c7.84-5.04 17.359-5.04 24.64 0l125.44 80.078c17.923 11.199 39.763-5.04 34.72-25.199l-37.52-143.36c-1.68-8.398 1.12-17.359 8.402-22.961h.002Z" fill-rule="nonzero"/></svg>
              <svg class="w-4 h-4 text-white fill-current" viewBox="0 0 534 509" xmlns="http://www.w3.org/2000/svg"><path d="m409.8 313.24 114.8-94.637c16.238-13.441 7.84-39.762-13.441-40.879l-147.84-8.96c-8.96-.56-16.801-6.161-20.16-14.56l-54.32-138.88c-7.84-19.602-35.281-19.602-43.121 0l-54.32 138.32c-3.36 8.399-11.199 14-20.16 14.56l-148.4 8.96c-21.281 1.121-29.68 27.441-13.441 40.879l114.8 94.078c6.719 5.602 10.078 15.121 7.84 23.52l-37.52 143.92c-5.04 20.16 16.8 36.398 34.719 25.199l124.88-80.078c7.84-5.04 17.359-5.04 24.64 0l125.44 80.078c17.923 11.199 39.763-5.04 34.72-25.199l-37.52-143.36c-1.68-8.398 1.12-17.359 8.402-22.961h.002Z" fill-rule="nonzero"/></svg>
              <svg class="w-4 h-4 text-white fill-current" viewBox="0 0 534 509" xmlns="http://www.w3.org/2000/svg"><path d="m409.8 313.24 114.8-94.637c16.238-13.441 7.84-39.762-13.441-40.879l-147.84-8.96c-8.96-.56-16.801-6.161-20.16-14.56l-54.32-138.88c-7.84-19.602-35.281-19.602-43.121 0l-54.32 138.32c-3.36 8.399-11.199 14-20.16 14.56l-148.4 8.96c-21.281 1.121-29.68 27.441-13.441 40.879l114.8 94.078c6.719 5.602 10.078 15.121 7.84 23.52l-37.52 143.92c-5.04 20.16 16.8 36.398 34.719 25.199l124.88-80.078c7.84-5.04 17.359-5.04 24.64 0l125.44 80.078c17.923 11.199 39.763-5.04 34.72-25.199l-37.52-143.36c-1.68-8.398 1.12-17.359 8.402-22.961h.002Z" fill-rule="nonzero"/></svg>
              <svg class="w-4 h-4 text-white fill-current" viewBox="0 0 534 509" xmlns="http://www.w3.org/2000/svg"><path d="m409.8 313.24 114.8-94.637c16.238-13.441 7.84-39.762-13.441-40.879l-147.84-8.96c-8.96-.56-16.801-6.161-20.16-14.56l-54.32-138.88c-7.84-19.602-35.281-19.602-43.121 0l-54.32 138.32c-3.36 8.399-11.199 14-20.16 14.56l-148.4 8.96c-21.281 1.121-29.68 27.441-13.441 40.879l114.8 94.078c6.719 5.602 10.078 15.121 7.84 23.52l-37.52 143.92c-5.04 20.16 16.8 36.398 34.719 25.199l124.88-80.078c7.84-5.04 17.359-5.04 24.64 0l125.44 80.078c17.923 11.199 39.763-5.04 34.72-25.199l-37.52-143.36c-1.68-8.398 1.12-17.359 8.402-22.961h.002Z" fill-rule="nonzero"/></svg>
            </div>
            <p class="text-gray-800 font-bold">أحمد محمد</p>
            <p class="mt-2">كاشير ساعدني في إدارة متجري بسهولة وكفاءة. نظام رائع!</p>
          </div>
          <div class="w-full flex items-end max-w-md h-auto relative">

            <img src="/assets/images/cashier-transaction-1.png" class="relative h-[40rem] lg:left-20">
          </div>
        </div>
      </div>
    </main>
  </section>
  <section id="about" class="bg-gray-800 text-white py-32">
    <div class="lg:max-w-3xl mx-auto text-center px-5 lg:px-0">
      <p class="text-4xl font-extrabold">عن كاشير</p>
      <p class="text-lg mt-5">كاشير هو نظام نقاط بيع سحابي متعدد المستأجرين مصمم للشركات الحديثة. مع ميزات متقدمة مثل إدارة المخزون في الوقت الفعلي، وتقارير شاملة، ودعم متعدد المستخدمين، يمكن لكاشير مساعدتك في تنمية عملك وتحسين كفاءتك التشغيلية.</p>
    </div>
  </section>
  <section id="product-menu" class="py-10 my-10 text-gray-400">
    <div class="xl:max-w-7xl lg:max-w-4xl mx-auto">
      <p class="text-4xl font-extrabold text-center text-gray-800">ميزات كاشير</p>
      <div class="lg:grid lg:grid-cols-3 grid-cols-2 my-10 gap-x-5">
        @foreach ($menu as $item)
          @if ($loop->iteration % 2 == 0)
            <div class="grid grid-cols-2 lg:grid-cols-none lg:block my-10 md:my-0">
              <div class="flex gap-x-5 justify-items-center lg:mb-10 sm:my-auto ml-10 lg:ml-0">
                <div class="h-10 min-w-2 bg-lakasir-primary rounded-lg"></div>
                <div class="lg:grid lg:grid-cols-1 xl:gap-y-3 lg:gap-y-1">
                  <p class="text-2xl text-gray-800 font-bold">{{ $item['title'] }}</p>
                  <p class="text-gray-400 w-4/5">{{ $item['description'] }}</p>
                </div>
              </div>
              <div class="flex justify-center">
                <img src="{{ $item['image'] }}" class="w-44">
              </div>
            </div>
          @else
            <div class="grid grid-cols-2 lg:grid-cols-none lg:block">
              <div class="flex justify-center">
                <img src="{{ $item['image'] }}" class="w-44">
              </div>
              <div class="flex gap-x-5 justify-items-center lg:mt-10 sm:my-auto">
                <div class="h-10 min-w-2 bg-lakasir-primary rounded-lg"></div>
                <div class="lg:grid lg:grid-cols-1 xl:gap-y-3 lg:gap-y-1">
                  <p class="text-2xl text-gray-800 font-bold">{{ $item['title'] }}</p>
                  <p class="text-gray-400 w-4/5">{{ $item['description'] }}</p>
                </div>
              </div>
            </div>
          @endif
        @endforeach
      </div>
    </div>
  </section>
  <section id="main-feature" class="bg-gray-800 text-white py-32">
    <div class="md:max-w-4xl max-w-1xl mx-auto">
      <div class="mb-20 grid grid-cols-1 gap-y-5">
        <p class="text-4xl font-extrabold text-center">مزايا كاشير</p>
        <p class="text-lg text-center sm:mx-0 mx-5">نظام SaaS متكامل مع جميع الميزات التي تحتاجها لإدارة نقاط البيع الخاصة بك</p>
      </div>
      <div class="grid sm:grid-cols-2 lg:gap-20 gap-10 px-10">
        @foreach($mainFeatures as $feature)
        <div class="flex gap-x-4">
          <div class="bg-lakasir-primary rounded-2xl flex justify-center items-center min-w-10 h-10">
            {!! $feature['icon'] !!}
          </div>
          <div>
            <p class="text-2xl font-bold">{{ $feature['title'] }}</p>
            <p class="text-gray-400 lg:w-4/5">{{ $feature['description'] }}</p>
          </div>
        </div>
        @endforeach
      </div>
    </div>
  </section>
  <section id="price">
    <div class="mx-auto max-w-7xl py-24 px-6 lg:px-8">
      <div class="sm:align-center sm:flex sm:flex-col">
        <p class="text-4xl font-extrabold text-center">خطط الأسعار</p>
        <p class="mt-5 text-xl text-gray-500 sm:text-center">اختر الخطة المناسبة لاحتياجات عملك - تجربة مجانية لمدة 14 يومًا</p>
      </div>
      <div class="mt-12 space-y-4 sm:mt-16 sm:grid sm:grid-cols-1 md:grid-cols-3 sm:gap-6 sm:space-y-0 mx-auto max-w-6xl xl:mx-0 xl:max-w-none justify-items-center">
        @foreach($prices as $price)
        <div class="divide-y divide-gray-200 rounded-lg border border-gray-200 shadow-sm w-80">
          <div class="p-6">
            <p class="text-lg font-medium leading-6 text-gray-900">{{ $price['title'] }}</p>
            <p class="mt-4 text-sm text-gray-500">{{ $price['description'] }}</p>
            <p class="mt-8">
              <span class="text-4xl font-bold tracking-tight text-gray-900">{{ $price['price'] }}</span> <span class="text-base font-medium text-gray-500">/bulan</span>
            </p>
            <a href="{{ route($price['route']) }}"
              class="mt-8 block w-full rounded-md border border-gray-800 bg-gray-800 py-2 text-center text-sm font-semibold text-white hover:bg-gray-900">
              {{ $price['button'] }}
            </a>
          </div>
          <div class="px-6 pt-6 pb-8">
            <h3 class="text-sm font-medium text-gray-900">يتضمن</h3>
            <ul role="list" class="mt-6 space-y-4">
              @foreach($price['includes'] as $include)
              <li class="flex space-x-3">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" class="h-5 w-5 flex-shrink-0 text-green-500">
                  <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd">
                  </path>
                </svg>
                <span class="text-sm text-gray-500">{{ $include }}</span>
              </li>
              @endforeach
              @foreach($price['excludes'] as $exclude)
              <li class="flex space-x-3">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5 flex-shrink-0 text-red-500">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>

                <span class="text-sm text-gray-500">{{ $exclude }}</span>
              </li>
              @endforeach
            </ul>
          </div>
        </div>
        @endforeach
      </div>
    </div>
  </section>
  <section id="cta" class="xl:max-w-7xl lg:max-w-3xl md:px-10 mx-auto text-white">
    <div class="bg-lakasir-primary text-center rounded-2xl my-20 p-10">
      <p class="font-extrabold text-3xl">ابدأ تجربتك المجانية اليوم</p>
      <p class="mt-5">14 يومًا مجانًا - لا حاجة لبطاقة ائتمان</p>
      <div class="mt-8">
        <a href="{{ route('auth.register') }}" class="inline-block rounded-md border border-transparent bg-white px-8 py-3 text-base font-medium text-lakasir-primary shadow hover:bg-gray-50">سجل الآن</a>
      </div>
    </div>
  </section>
  <section id="footer" class="bg-gray-800 text-white py-10 flex justify-center gap-x-3">
    <p>كاشير - صنع بـ </p>
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6">
      <path d="m11.645 20.91-.007-.003-.022-.012a15.247 15.247 0 0 1-.383-.218 25.18 25.18 0 0 1-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0 1 12 5.052 5.5 5.5 0 0 1 16.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 0 1-4.244 3.17 15.247 15.247 0 0 1-.383.219l-.022.012-.007.004-.003.001a.752.752 0 0 1-.704 0l-.003-.001Z" />
    </svg>
  </section>
</div>
