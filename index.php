<?php
// Include the bootstrap header to initialize the data layer and HTML head
require_once 'header.php';

// Use the pre-instantiated service concierge to fetch all 904 records
$landmarks = $landmarkService->getAll();
?>

<h2>Master Registry Catalog</h2>
<p>Displaying all historic properties currently logged in the system ledger.</p>

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