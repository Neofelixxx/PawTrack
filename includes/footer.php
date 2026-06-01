</div> <footer class="bg-white border-t border-slate-100 mt-20">
    <div class="max-w-7xl mx-auto px-6 py-12 flex flex-col md:flex-row justify-between items-center gap-6 text-sm text-slate-500">
        <div class="flex items-center gap-2 font-semibold text-slate-700">
            <span>🐾</span> <span>PawTrack Systems</span>
        </div>
        <div class="flex flex-wrap justify-center gap-6 font-medium text-slate-500">
            <a href="/PawTrack/cats/list.php" class="hover:text-sky-500 transition duration-150">Adopt</a>
            <a href="/PawTrack/reports/index.php" class="hover:text-sky-500 transition duration-150">Analytics</a>
            <a href="/PawTrack/intake/map.php" class="hover:text-sky-500 transition duration-150">GIS Hotspots</a>
        </div>
        <p class="text-xs text-center md:text-right text-slate-400 font-medium">
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