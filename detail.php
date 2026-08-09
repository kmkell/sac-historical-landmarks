<?php
require_once 'header.php';

// Validate ID parameter from URL as an integer
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$landmark = null;
$styles = [];

if ($id > 0) {
    $landmark = $landmarkService->getById($id);
    if ($landmark) {
        $styles = $landmarkService->getStylesForLandmark($id);
    }
}
?>

<?php if ($landmark): ?>
    <div style="margin-bottom: 20px;">
        <a href="index.php" style="color: #34495e; text-decoration: none; font-weight: bold;">&laquo; Back to Registry Catalog</a>
    </div>

    <article style="background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <h2 style="margin-top: 0; color: #2c3e50; border-bottom: 2px solid #ecf0f1; padding-bottom: 10px;">
            <?php echo htmlspecialchars($landmark['resource_name']); ?>
        </h2>

        <table style="width: 100%; border-collapse: collapse; margin-top: 15px;">
            <tr>
                <td style="padding: 10px; width: 200px; font-weight: bold; background: #f8f9fa; border-bottom: 1px solid #ddd;">Object ID:</td>
                <td style="padding: 10px; border-bottom: 1px solid #ddd;"><?php echo htmlspecialchars($landmark['objectid']); ?></td>
            </tr>
            <tr>
                <td style="padding: 10px; font-weight: bold; background: #f8f9fa; border-bottom: 1px solid #ddd;">Street Address:</td>
                <td style="padding: 10px; border-bottom: 1px solid #ddd;"><?php echo htmlspecialchars($landmark['street_address']); ?></td>
            </tr>
            <tr>
                <td style="padding: 10px; font-weight: bold; background: #f8f9fa; border-bottom: 1px solid #ddd;">Parcel Number (APN):</td>
                <td style="padding: 10px; border-bottom: 1px solid #ddd;">
                    <?php if ($landmark['apn'] === 'UNKNOWN'): ?>
                        <span class="badge">UNKNOWN</span>
                    <?php else: ?>
                        <?php echo htmlspecialchars($landmark['apn']); ?>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <td style="padding: 10px; font-weight: bold; background: #f8f9fa; border-bottom: 1px solid #ddd;">City Ordinance:</td>
                <td style="padding: 10px; border-bottom: 1px solid #ddd;"><?php echo htmlspecialchars($landmark['ordinance']); ?></td>
            </tr>
            <tr>
                <td style="padding: 10px; font-weight: bold; background: #f8f9fa; border-bottom: 1px solid #ddd;">Gept spatial Area (Sq Ft):</td>
                <td style="padding: 10px; border-bottom: 1px solid #ddd;"><?php echo htmlspecialchars(number_format((float)$landmark['shape__area'], 2)); ?> sq ft</td>
            </tr>
            <tr>
                <td style="padding: 10px; font-weight: bold; background: #f8f9fa; border-bottom: 1px solid #ddd;">Spatial Perimeter Length:</td>
                <td style="padding: 10px; border-bottom: 1px solid #ddd;"><?php echo htmlspecialchars(number_format((float)$landmark['shape__length'], 2)); ?> ft</td>
            </tr>
        </table>
    </article>

<?php else: ?>
    <div style="background: #f8d7da; color: #721c24; padding: 20px; border-radius: 5px; margin-top: 20px;">
        <h3>Record Not Found</h3>
        <p>The historical landmark ID you requested could not be found or was not specified.</p>
        <a href="index.php" style="color: #721c24; font-weight: bold;">Return to Catalog</a>
    </div>
<?php endif; ?>

</body>
</html>