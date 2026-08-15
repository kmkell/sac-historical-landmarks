<?php
require_once 'header.php';

// Validate ID parameter from URL as an integer
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$landmark = null;
$styles = [];

if ($id > 0) {
    // This method should now fetch both city data AND the joined landmark_research fields
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

    <article style="background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px;">
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
                <td style="padding: 10px; font-weight: bold; background: #f8f9fa; border-bottom: 1px solid #ddd;">Shape Spatial Area (Sq Ft):</td>
                <td style="padding: 10px; border-bottom: 1px solid #ddd;"><?php echo htmlspecialchars(number_format((float)$landmark['shape__area'], 2)); ?> sq ft</td>
            </tr>
            <tr>
                <td style="padding: 10px; font-weight: bold; background: #f8f9fa; border-bottom: 1px solid #ddd;">Spatial Perimeter Length:</td>
                <td style="padding: 10px; border-bottom: 1px solid #ddd;"><?php echo htmlspecialchars(number_format((float)$landmark['shape__length'], 2)); ?> ft</td>
            </tr>
        </table>
    </article>

    <!-- NEW RESEARCH & STYLES SECTION -->
     <div style="float: right;">
    <a href="edit_research.php?id=<?php echo $id; ?>" style="background: #2980b9; color: white; padding: 6px 12px; border-radius: 4px; text-decoration: none; font-size: 14px;">Edit Research</a>
</div>
    <article style="background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <h3 style="margin-top: 0; color: #2c3e50; border-bottom: 2px solid #ecf0f1; padding-bottom: 10px;">
            Research & History Notes
        </h3>

        <table style="width: 100%; border-collapse: collapse; margin-top: 15px;">
            <tr>
                <td style="padding: 10px; width: 200px; font-weight: bold; background: #f8f9fa; border-bottom: 1px solid #ddd;">Year Built:</td>
                <td style="padding: 10px; border-bottom: 1px solid #ddd;">
                    <?php echo htmlspecialchars($landmark['year_built'] ?? 'Not yet recorded'); ?>
                </td>
            </tr>
            <tr>
                <td style="padding: 10px; font-weight: bold; background: #f8f9fa; border-bottom: 1px solid #ddd;">Architect:</td>
                <td style="padding: 10px; border-bottom: 1px solid #ddd;">
                    <?php echo htmlspecialchars($landmark['architect'] ?? 'Not yet recorded'); ?>
                </td>
            </tr>
            <tr>
                <td style="padding: 10px; font-weight: bold; background: #f8f9fa; border-bottom: 1px solid #ddd;">Architectural Styles:</td>
                <td style="padding: 10px; border-bottom: 1px solid #ddd;">
                    <?php if (!empty($styles)): ?>
                        <ul style="margin: 0; padding-left: 20px;">
                            <?php foreach ($styles as $style): ?>
    <li>
        <?php echo htmlspecialchars($style['style_name']); ?> (<?php echo htmlspecialchars($style['era']); ?>)
        <?php if (!empty($style['is_primary']) && $style['is_primary'] == 1): ?>
            <span class="style-badge-primary">Primary</span>
        <?php endif; ?>
    </li>
<?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <span style="color: #7f8c8d; font-style: italic;">No architectural styles assigned yet.</span>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <td style="padding: 10px; font-weight: bold; background: #f8f9fa; border-bottom: 1px solid #ddd; vertical-align: top;">Notes:</td>
                <td style="padding: 10px; border-bottom: 1px solid #ddd;">
                    <?php echo !empty($landmark['notes']) ? nl2br(htmlspecialchars($landmark['notes'])) : '<span style="color: #7f8c8d; font-style: italic;">No research notes added yet.</span>'; ?>
                </td>
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