</div> <footer class="bg-white border-t border-slate-100 mt-20 no-print">
    <div class="max-w-7xl mx-auto px-6 py-12 flex flex-col md:flex-row justify-between items-center gap-6 text-sm text-slate-500">
        <div class="flex items-center gap-2 font-semibold text-slate-700">
            <svg class="w-5 h-5 text-slate-600 transition duration-150" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 14c-1.66 0-3 1.12-3 2.5 0 2.48 2.5 4.5 3 4.5s3-2.02 3-4.5c0-1.38-1.34-2.5-3-2.5zm-4.5-3c-.83 0-1.5.84-1.5 1.88 0 1.87 1.25 3.37 1.5 3.37s1.5-1.5 1.5-3.37c0-1.04-.67-1.88-1.5-1.88zm9 0c-.83 0-1.5.84-1.5 1.88 0 1.87 1.25 3.37 1.5 3.37s1.5-1.5 1.5-3.37c0-1.04-.67-1.88-1.5-1.88zm-7.5-4c-.83 0-1.5.84-1.5 1.88 0 1.87 1.25 3.38 1.5 3.38s1.5-1.51 1.5-3.38c0-1.04-.67-1.88-1.5-1.88zm6 0c-.83 0-1.5.84-1.5 1.88 0 1.87 1.25 3.38 1.5 3.38s1.5-1.51 1.5-3.38c0-1.04-.67-1.88-1.5-1.88z"/>
            </svg>
            <span class="tracking-tight">PawTrack Core Platform</span>
        </div>
        
        <div class="flex flex-wrap justify-center gap-6 font-medium text-slate-500">
            <a href="<?php echo $base_path ?? '/PawTrack/'; ?>cats/list.php" class="hover:text-sky-500 transition duration-150">Adopt</a>
            <a href="<?php echo $base_path ?? '/PawTrack/'; ?>donations/add.php" class="hover:text-sky-500 transition duration-150">Donate</a>
            <a href="<?php echo $base_path ?? '/PawTrack/'; ?>intake/map.php" class="hover:text-sky-500 transition duration-150">Map</a>
        </div>
        
        <div class="text-center md:text-right flex flex-col items-center md:items-end gap-0.5">
            <p class="text-xs text-slate-500 font-semibold">
                &copy; <?php echo date('Y'); ?> PawTrack Core Engine. All rights reserved.
            </p>
            <p class="text-[10px] font-mono text-slate-400">
                v1.5.0-rbac · Deployment Node: MY-JHB-01
            </p>
        </div>
    </div>
</footer>
</body>
</html>