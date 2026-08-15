<?php
require_once 'header.php';

// Validate ID
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    die("Invalid landmark ID.");
}

// Fetch existing data for this landmark
$landmark = $landmarkService->getById($id);
if (!$landmark) {
    die("Landmark not found.");
}

// Fetch all available architectural styles for the checkboxes
$allStyles = $landmarkService->getAllStyles();

// Fetch styles already assigned to this landmark
$currentStyles = $landmarkService->getStylesForLandmark($id);
?>

<div style="max-width: 800px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <div style="margin-bottom: 20px;">
        <a href="detail.php?id=<?php echo $id; ?>" style="color: #34495e; text-decoration: none; font-weight: bold;">&laquo; Back to Landmark Details</a>
    </div>

    <h2 style="margin-top: 0; color: #2c3e50; border-bottom: 2px solid #ecf0f1; padding-bottom: 10px;">
        Edit Research: <?php echo htmlspecialchars($landmark['resource_name']); ?>
    </h2>

    <p style="color: #7f8c8d; margin-bottom: 20px;">
        <strong>Address:</strong> <?php echo htmlspecialchars($landmark['street_address']); ?>
    </p>

    <form action="update_research.php" method="POST">
        <!-- Hidden input to pass the landmark ID along with the form -->
        <input type="hidden" name="landmark_id" value="<?php echo $id; ?>">

        <div style="margin-bottom: 20px;">
            <label style="display: block; font-weight: bold; margin-bottom: 5px;">Year Built:</label>
            <input type="text" name="year_built" value="<?php echo htmlspecialchars($landmark['year_built'] ?? ''); ?>" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; font-weight: bold; margin-bottom: 5px;">Architect:</label>
            <input type="text" name="architect" value="<?php echo htmlspecialchars($landmark['architect'] ?? ''); ?>" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
        </div>

       <!-- Architectural Styles & Designations -->
        <div style="margin-bottom: 20px;">
            <label style="display: block; font-weight: bold; margin-bottom: 8px;">Architectural Styles & Designations:</label>
            <div style="background: #f8f9fa; padding: 15px; border: 1px solid #ddd; border-radius: 4px; max-height: 250px; overflow-y: auto;">
                <?php if (!empty($allStyles)): ?>
                    <?php 
                        $currentStyleMap = [];
                        if (!empty($currentStyles)) {
                            foreach ($currentStyles as $cs) {
                                if (isset($cs['style_id'])) {
                                    $currentStyleMap[$cs['style_id']] = $cs['is_primary'];
                                }
                            }
                        }
                    ?>
                    <?php foreach ($allStyles as $style): ?>
                        <?php 
                            $styleId = $style['style_id'];
                            $isSelected = isset($currentStyleMap[$styleId]);
                            $isPrimaryVal = $isSelected ? $currentStyleMap[$styleId] : 0;
                        ?>
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px; border-bottom: 1px solid #eee; padding-bottom: 8px;">
                            <label style="font-weight: normal; cursor: pointer; flex-grow: 1;">
                                <input type="checkbox" name="styles[<?php echo $styleId; ?>][selected]" value="1" 
                                    <?php echo $isSelected ? 'checked' : ''; ?>>
                                <?php echo htmlspecialchars($style['style_name']); ?> 
                                <span style="color: #7f8c8d; font-size: 12px;">(<?php echo htmlspecialchars($style['era']); ?>)</span>
                            </label>
                            
                            <div>
                                <select name="styles[<?php echo $styleId; ?>][is_primary]" style="padding: 4px; border: 1px solid #ddd; border-radius: 4px; font-size: 12px;">
                                    <option value="0" <?php echo ($isPrimaryVal == 0) ? 'selected' : ''; ?>>Secondary / Other</option>
                                    <option value="1" <?php echo ($isPrimaryVal == 1) ? 'selected' : ''; ?>>Primary Style</option>
                                </select>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="color: #7f8c8d; margin: 0;">No architectural styles found in the database.</p>
                <?php endif; ?>
            </div>
        </div>
        <div style="margin-bottom: 20px;">
            <label style="display: block; font-weight: bold; margin-bottom: 5px;">Research & History Notes:</label>
            <textarea name="notes" rows="6" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;"><?php echo htmlspecialchars($landmark['notes'] ?? ''); ?></textarea>
        </div>

        <div style="margin-top: 30px;">
            <button type="submit" style="background: #27ae60; color: white; padding: 12px 20px; border: none; border-radius: 4px; font-weight: bold; cursor: pointer;">Save Changes</button>
            <a href="detail.php?id=<?php echo $id; ?>" style="margin-left: 15px; color: #7f8c8d; text-decoration: none;">Cancel</a>
        </div>
    </form>
</div>

</body>
</html>