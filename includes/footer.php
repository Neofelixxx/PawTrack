</div> <!-- Closes Content Layout Wrapper -->

<!-- SYSTEM FOOTER -->
<footer class="bg-white border-t border-slate-100 mt-20">
    <div class="max-w-7xl mx-auto px-6 py-12 flex flex-col md:flex-row justify-between items-center gap-6 text-sm text-slate-500">
        <!-- Brand / Identity Grouping -->
        <div class="flex items-center gap-2 font-semibold text-slate-700">
            <span>🐾</span> <span class="tracking-tight">PawTrack Core Platform</span>
        </div>
        
        <!-- Navigation Core Hub links -->
        <div class="flex flex-wrap justify-center gap-6 font-medium text-slate-500">
            <a href="/PawTrack/cats/list.php" class="hover:text-sky-500 transition duration-150">Adopt</a>
            <a href="/PawTrack/reports/index.php" class="hover:text-sky-500 transition duration-150">Analytics</a>
            <a href="/PawTrack/intake/map.php" class="hover:text-sky-500 transition duration-150">GIS Hotspots</a>
        </div>
        
        <!-- Production Enterprise Metadata -->
        <div class="text-center md:text-right flex flex-col items-center md:items-end gap-0.5">
            <p class="text-xs text-slate-500 font-semibold">
                &copy; <?php echo date('Y'); ?> PawTrack Core Engine. All rights reserved.
            </p>
            <p class="text-[10px] font-mono text-slate-400">
                v1.4.2-stable · Deployment Node: MY-JHB-01
            </p>
        </div>
    </div>
</footer>

<!-- SIDEBAR TOGGLE MECHANICS -->
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