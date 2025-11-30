<?php
$prefix = asset_prefix();
?>
    </div> <!-- /.container -->
</main>
<footer class="bg-light py-3 mt-auto border-top">
    <div class="container text-center text-muted small">
        &copy; <?= date('Y') ?> Perpustakaan Digital
    </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script src="<?= htmlspecialchars($prefix . 'assets/js/app.js', ENT_QUOTES) ?>"></script>
</body>
</html>
