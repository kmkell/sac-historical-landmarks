<?php
// Centralized bootstrap initialization
require_once 'header.php';

// 1. Process search query parameter
$searchTerm = isset($_GET['q']) ? trim($_GET['q']) : '';

// 2. Pagination Configuration
$recordsPerPage = 25;
$currentPage = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;

// 3. Fetch total record count and calculate total pages
$totalRecords = $landmarkService->getTotalCount($searchTerm);
$totalPages = ceil($totalRecords / $recordsPerPage);

// Re-bound current page if out of range
if ($currentPage > $totalPages && $totalPages > 0) {
    $currentPage = $totalPages;
}

// Calculate SQL OFFSET
$offset = ($currentPage - 1) * $recordsPerPage;

// 4. Fetch paginated dataset
$landmarks = $landmarkService->getPaginated($recordsPerPage, $offset, $searchTerm);
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

<p>
    Showing <?php echo count($landmarks); ?> of <?php echo $totalRecords; ?> properties
    <?php if ($searchTerm !== ''): ?>
        matching "<strong><?php echo htmlspecialchars($searchTerm); ?></strong>"
    <?php endif; ?>
    (Page <?php echo $currentPage; ?> of <?php echo max(1, $totalPages); ?>)
</p>

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
                    <td>
                        <a href="detail.php?id=<?php echo urlencode($row['id']); ?>" class="landmark-link">
    <?php echo htmlspecialchars($row['resource_name']); ?>
</a>
                    </td>
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

<?php if ($totalPages > 1): ?>
    <div class="pagination">
        <?php if ($currentPage > 1): ?>
            <a href="index.php?page=<?php echo ($currentPage - 1); ?>&q=<?php echo urlencode($searchTerm); ?>">&laquo; Previous</a>
        <?php else: ?>
            <span class="disabled">&laquo; Previous</span>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <?php if ($i == $currentPage): ?>
                <span class="active"><?php echo $i; ?></span>
            <?php else: ?>
                <a href="index.php?page=<?php echo $i; ?>&q=<?php echo urlencode($searchTerm); ?>"><?php echo $i; ?></a>
            <?php endif; ?>
        <?php endfor; ?>

        <?php if ($currentPage < $totalPages): ?>
            <a href="index.php?page=<?php echo ($currentPage + 1); ?>&q=<?php echo urlencode($searchTerm); ?>">Next &raquo;</a>
        <?php else: ?>
            <span class="disabled">Next &raquo;</span>
        <?php endif; ?>
    </div>
<?php endif; ?>

</body>
</html>