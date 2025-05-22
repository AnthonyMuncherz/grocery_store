</div><!-- /.container -->

<footer class="bg-gray-800 text-gray-300 mt-auto py-8">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div>
                <h5 class="text-xl font-semibold text-white mb-3"><?php echo APP_NAME; ?></h5>
                <p class="text-sm">Your trusted Malaysian grocery store.</p>
            </div>
            <div>
                <h5 class="text-lg font-semibold text-white mb-3">Quick Links</h5>
                <ul class="space-y-2 text-sm">
                    <li><a href="index.php?module=products" class="hover:text-white">Products</a></li>
                    <li><a href="index.php?module=orders" class="hover:text-white">Orders</a></li>
                    <li><a href="index.php?module=inventory" class="hover:text-white">Inventory</a></li>
                </ul>
            </div>
            <div>
                <h5 class="text-lg font-semibold text-white mb-3">Contact Us</h5>
                <address class="text-sm not-italic">
                    123 Jalan Sample<br>
                    Taman Test, 50000<br>
                    Kuala Lumpur, Malaysia<br>
                    <i class="fas fa-phone mr-2"></i> <span class="my-phone">+60 12-345 6789</span><br>
                    <i class="fas fa-envelope mr-2"></i> info@grocerystore.my
                </address>
            </div>
        </div>
        <hr class="border-gray-700 my-6">
        <div class="text-center text-sm">
            <p>&copy; <?php echo date('Y'); ?> <?php echo APP_NAME; ?>. All rights reserved.</p>
        </div>
    </div>
</footer>

<!-- Bootstrap JS (Still needed for data-bs-dismiss, tooltips if used) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- Custom JS -->
<script src="assets/js/main.js"></script>
<script>
    // Mobile menu toggle
    const mobileMenuButton = document.getElementById('mobile-menu-button');
    const mobileMenu = document.getElementById('mobile-menu');
    if (mobileMenuButton && mobileMenu) {
        mobileMenuButton.addEventListener('click', () => {
            const expanded = mobileMenuButton.getAttribute('aria-expanded') === 'true' || false;
            mobileMenuButton.setAttribute('aria-expanded', !expanded);
            mobileMenu.classList.toggle('hidden');
        });
    }
</script>
</body>

</html>