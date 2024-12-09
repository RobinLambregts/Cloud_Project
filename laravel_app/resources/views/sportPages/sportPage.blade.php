<div>
    <?php foreach ($sportLijst as $sport): ?>
        <li id="evenementen">
            <p><?php echo htmlspecialchars($sport['naam']); ?></p>
            <p><?php echo htmlspecialchars($sport['locatie']); ?></p>
        </li>
    <?php endforeach; ?>
</div>