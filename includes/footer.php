</div> <!-- Closes Content Layout Wrapper -->

<!-- SYSTEM FOOTER -->
<footer class="bg-white border-t border-gray-100 mt-20">
    <div class="max-w-7xl mx-auto px-6 py-12 flex flex-col md:flex-row justify-between items-center gap-6 text-sm text-gray-500">
        <div class="flex items-center gap-2 font-medium text-gray-700">
            <span>🐾</span> <span>PawTrack Systems</span>
        </div>
        <div class="flex flex-wrap justify-center gap-6">
            <a href="/PawTrack/cats/list.php" class="hover:text-orange-500 transition">Adopt</a>
            <a href="/PawTrack/donation/add.php" class="hover:text-orange-500 transition">Sponsor</a>
            <a href="#" class="hover:text-orange-500 transition">Volunteer</a>
            <a href="#" class="hover:text-orange-500 transition">Privacy Policy</a>
        </div>
        <p class="text-xs text-center md:text-right text-gray-400">
            &copy; <?php echo date('Y'); ?> PSM Project. Developed for academic validation.
        </p>
    </div>
</footer>

<script>
function toggleSidebar() {
    const sidebar = document.getElementById("sidebar");
    const backdrop = document.getElementById("backdrop");
    if(sidebar && backdrop) {
        sidebar.classList.toggle("-translate-x-full");
        backdrop.classList.toggle("hidden");
    }
}
</script>
</body>
</html>