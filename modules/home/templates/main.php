<?php
/**
 * Home Page Template
 * Features a hero section, featured categories, and why choose us.
 */
?>
<div class="home-page">
    <!-- Hero Section -->
    <section class="hero-section relative bg-gray-700 text-white overflow-hidden">
        <div class="absolute inset-0">
            <!-- Replace with a dynamic, high-quality background image -->
            <img src="assets/images/hero-bg.jpg" alt="Vibrant Malaysian Market"
                class="w-full h-full object-cover opacity-50">
            <!-- Fallback if image is missing, or use a solid color -->
            <!-- <div class="w-full h-full bg-malaysia-blue opacity-80"></div> -->
        </div>
        <div class="relative container mx-auto px-6 py-24 md:py-32 lg:py-48 text-center z-10">
            <div class="animate-on-scroll animate-fade-in-up" data-animation-delay="100">
                <h1 class="text-4xl sm:text-5xl md:text-6xl font-bold mb-6 leading-tight">
                    Selamat Datang ke <span class="text-yellow-400"><?php echo APP_NAME; ?></span>!
                </h1>
            </div>
            <div class="animate-on-scroll animate-fade-in-up" data-animation-delay="300">
                <p class="text-lg md:text-xl mb-10 max-w-2xl mx-auto">
                    Your one-stop shop for authentic Malaysian flavors, fresh groceries, and everyday essentials.
                </p>
            </div>
            <div class="animate-on-scroll animate-fade-in-up" data-animation-delay="500">
                <a href="index.php?module=products"
                    class="bg-yellow-500 hover:bg-yellow-600 text-gray-900 font-semibold py-3 px-8 rounded-lg text-lg shadow-lg transition-transform transform hover:scale-105">
                    <i class="fas fa-shopping-bag mr-2"></i> Explore Products
                </a>
            </div>
        </div>
    </section>

    <!-- Why Choose Us Section -->
    <section class="why-choose-us py-16 lg:py-24 bg-gray-50">
        <div class="container mx-auto px-6">
            <div class="text-center mb-12 lg:mb-16 animate-on-scroll animate-fade-in">
                <h2 class="text-3xl md:text-4xl font-semibold text-gray-800">Kenapa Pilih Kami?</h2>
                <p class="text-gray-600 mt-2 text-lg">Experience the best of Malaysia, delivered to you.</p>
            </div>
            <div class="grid md:grid-cols-3 gap-8 lg:gap-12">
                <div class="feature-item text-center p-6 bg-white rounded-xl shadow-lg animate-on-scroll animate-slide-in-up"
                    data-animation-delay="0">
                    <div class="text-malaysia-blue text-5xl mb-4">
                        <i class="fas fa-leaf"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Fresh & Authentic</h3>
                    <p class="text-gray-600">
                        Sourcing directly to bring you the freshest produce and genuine Malaysian products.
                    </p>
                </div>
                <div class="feature-item text-center p-6 bg-white rounded-xl shadow-lg animate-on-scroll animate-slide-in-up"
                    data-animation-delay="200">
                    <div class="text-malaysia-blue text-5xl mb-4">
                        <i class="fas fa-shipping-fast"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Convenient Shopping</h3>
                    <p class="text-gray-600">
                        Easy online ordering and reliable delivery to your doorstep.
                    </p>
                </div>
                <div class="feature-item text-center p-6 bg-white rounded-xl shadow-lg animate-on-scroll animate-slide-in-up"
                    data-animation-delay="400">
                    <div class="text-malaysia-blue text-5xl mb-4">
                        <i class="fas fa-smile-beam"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Trusted Service</h3>
                    <p class="text-gray-600">
                        Committed to providing excellent customer service and a delightful shopping experience.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Categories Section -->
    <section class="featured-categories py-16 lg:py-24 bg-white">
        <div class="container mx-auto px-6">
            <div class="text-center mb-12 lg:mb-16 animate-on-scroll animate-fade-in">
                <h2 class="text-3xl md:text-4xl font-semibold text-gray-800">Discover Our Range</h2>
                <p class="text-gray-600 mt-2 text-lg">Handpicked selections just for you.</p>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 lg:gap-8">
                <?php
                // Example categories - replace with dynamic data if available
                // Or ensure category IDs are correct if hardcoding.
                $sampleCategories = [
                    ['id' => 1, 'name' => 'Fresh Produce', 'image' => 'assets/images/category-produce.jpg', 'description' => 'Farm-fresh fruits and vegetables.', 'icon' => 'fa-carrot'],
                    ['id' => 2, 'name' => 'Pantry Staples', 'image' => 'assets/images/category-pantry.jpg', 'description' => 'Rice, noodles, sauces, and more.', 'icon' => 'fa-pepper-hot'],
                    ['id' => 4, 'name' => 'Delicious Snacks', 'image' => 'assets/images/category-snacks.jpg', 'description' => 'Authentic Malaysian kuih and snacks.', 'icon' => 'fa-cookie-bite'],
                    ['id' => 3, 'name' => 'Refreshing Beverages', 'image' => 'assets/images/category-drinks.jpg', 'description' => 'Local favorites like Teh Tarik and Sirap Bandung.', 'icon' => 'fa-mug-hot'],
                ];
                foreach ($sampleCategories as $index => $category): ?>
                    <div class="category-card bg-gray-50 rounded-xl shadow-lg overflow-hidden flex flex-col transition-all duration-300 hover:shadow-2xl hover:-translate-y-2 animate-on-scroll animate-fade-in"
                        data-animation-delay="<?= $index * 150 ?>">
                        <div class="h-48 w-full overflow-hidden">
                            <img src="<?= htmlspecialchars($category['image']) ?>"
                                alt="<?= htmlspecialchars($category['name']) ?>"
                                class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-110">
                        </div>
                        <div class="p-6 flex flex-col flex-grow">
                            <div class="text-malaysia-blue text-3xl mb-3"><i class="fas <?= $category['icon'] ?>"></i></div>
                            <h3 class="text-xl font-semibold text-gray-800 mb-2"><?= htmlspecialchars($category['name']) ?>
                            </h3>
                            <p class="text-gray-600 text-sm mb-4 flex-grow">
                                <?= htmlspecialchars($category['description']) ?></p>
                            <a href="index.php?module=products&category=<?= $category['id'] ?>"
                                class="mt-auto inline-block bg-malaysia-blue hover:bg-malaysia-blue-dark text-white font-medium py-2 px-4 rounded-md text-center transition-colors">
                                Shop <?= htmlspecialchars($category['name']) ?>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Call to Action Section -->
    <section class="cta-section py-16 lg:py-24 bg-malaysia-blue text-white">
        <div class="container mx-auto px-6 text-center animate-on-scroll animate-fade-in">
            <h2 class="text-3xl md:text-4xl font-semibold mb-6">Ready to Taste Malaysia?</h2>
            <p class="text-lg md:text-xl mb-10 max-w-xl mx-auto">
                Browse our extensive collection of groceries and bring the taste of home to your kitchen.
            </p>
            <a href="index.php?module=products"
                class="bg-yellow-500 hover:bg-yellow-600 text-gray-900 font-semibold py-3 px-10 rounded-lg text-lg shadow-lg transition-transform transform hover:scale-105">
                Start Shopping Now
            </a>
        </div>
    </section>
</div>

<style>
    /* Custom Animations (can be moved to style.css) */
    .animate-fade-in-up {
        opacity: 0;
        transform: translateY(20px);
        transition: opacity 0.6s ease-out, transform 0.6s ease-out;
    }

    .animate-fade-in {
        opacity: 0;
        transition: opacity 0.8s ease-out;
    }

    .animate-slide-in-up {
        /* For features, if different from fade-in-up */
        opacity: 0;
        transform: translateY(30px);
        transition: opacity 0.5s ease-out, transform 0.5s ease-out;
    }

    /* When element is visible */
    .is-visible .animate-fade-in-up,
    .is-visible.animate-fade-in-up,
    /* if is-visible is on parent */
    .is-visible .animate-fade-in,
    .is-visible.animate-fade-in,
    .is-visible .animate-slide-in-up,
    .is-visible.animate-slide-in-up {
        opacity: 1;
        transform: translateY(0);
    }
</style>