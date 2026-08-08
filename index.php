<?php
// Include the bootstrap header
require_once 'header.php';

// Check if a search term was submitted via URL query string
$searchTerm = isset($_GET['q']) ? trim($_GET['q']) : '';

// Execute either search query or fetch all
if ($searchTerm !== '') {
    $landmarks = $landmarkService->search($searchTerm);
} else {
    $landmarks = $landmarkService->getAll();
}
?>

<h2>Master Registry Catalog</h2>

<form action="index.php" method="GET" style="margin-bottom: 20px;">
    <input 
        type="text" 
        name="q" 
        placeholder="Search by address or property name..." 
        value="<?php echo htmlspecialchars($searchTerm); ?>" 
        style="padding: 8px 12px; width: 300px; border: 1px solid #ccc; border-radius: 4px;"
    >
    <button type="submit" style="padding: 8px 16px; background-color: #2c3e50; color: #fff; border: none; border-radius: 4px; cursor: pointer;">
        Search
    </button>
    <?php if ($searchTerm !== ''): ?>
        <a href="index.php" style="margin-left: 10px; color: #e74c3c; text-decoration: none;">Clear Search</a>
    <?php endif; ?>
</form>

<?php if ($searchTerm !== ''): ?>
    <p>Showing results for: <strong><?php echo htmlspecialchars($searchTerm); ?></strong> (<?php echo count($landmarks); ?> found)</p>
<?php endif; ?>

<table class="data-table">
    <thead>
        <tr>
            <th>Object ID</th>
            <th>Resource Name</th>
            <th>Street Address</th>
            <th>Ordinance</th>
            <th>APN</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($landmarks)): ?>
            <?php foreach ($landmarks as $row): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['objectid']); ?></td>
                    <td><strong><?php echo htmlspecialchars($row['resource_name']); ?></strong></td>
                    <td><?php echo htmlspecialchars($row['street_address']); ?></td>
                    <td><?php echo htmlspecialchars($row['ordinance']); ?></td>
                    <td>
                        <?php if ($row['apn'] === 'UNKNOWN'): ?>
                            <span class="badge">UNKNOWN</span>
                        <?php else: ?>
                            <?php echo htmlspecialchars($row['apn']); ?>                     
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="5">No historical landmark records found in the system.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

</body>
</html>