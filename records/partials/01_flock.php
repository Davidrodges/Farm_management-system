<!-- 1. WEEKLY FLOCK STATUS -->
<div class="record-section">
    <div class="section-header">1. Weekly Flock Status 🐔</div>
    <div class="section-body">
        <div class="dashboard-grid">
            <div class="form-group">
                <label>Age of Birds (Weeks)</label>
                <input type="number" name="age_weeks" value="<?php echo isset($flock['age_weeks']) ? $flock['age_weeks'] : ''; ?>" placeholder="0">
            </div>
            <div class="form-group">
                <label>Opening Number</label>
                <input type="number" name="opening_birds" value="<?php echo isset($flock['opening_birds']) ? $flock['opening_birds'] : ''; ?>" placeholder="0">
            </div>
            <div class="form-group">
                <label>Birds Added</label>
                <input type="number" name="added_birds" value="<?php echo isset($flock['added_birds']) ? $flock['added_birds'] : ''; ?>" placeholder="0">
            </div>
            <div class="form-group">
                <label>Sold / Culls</label>
                <input type="number" name="sold_culls" value="<?php echo isset($flock['sold_culls']) ? $flock['sold_culls'] : ''; ?>" placeholder="0">
            </div>
            <div class="form-group">
                <label>Deaths</label>
                <input type="number" name="dead_birds" value="<?php echo isset($flock['dead_birds']) ? $flock['dead_birds'] : ''; ?>" placeholder="0">
            </div>
             <div class="form-group">
                <label>Isolated (Sick)</label>
                <input type="number" name="isolated_birds" value="<?php echo isset($flock['isolated_birds']) ? $flock['isolated_birds'] : ''; ?>" placeholder="0">
            </div>
            <div class="form-group" style="grid-column: span 2;">
                <label>Cause of Deaths</label>
                <input type="text" name="cause_of_death" value="<?php echo isset($flock['cause_of_death']) ? $flock['cause_of_death'] : ''; ?>" placeholder="Describe if any">
            </div>
            <div class="form-group">
                <label>Closing Number (Manual Check)</label>
                <input type="number" name="closing_birds" value="<?php echo isset($flock['closing_birds']) ? $flock['closing_birds'] : ''; ?>" placeholder="0">
            </div>
        </div>
    </div>
</div>
