<!-- 6. WEEKLY BIOSECURITY -->
<div class="record-section">
    <div class="section-header">6. Weekly Biosecurity 🧼</div>
    <div class="section-body">
        <div class="dashboard-grid">
            <div class="form-group">
                <label>Disinfectant Used</label>
                <input type="text" name="disinfectant_used" value="<?php echo isset($bio['disinfectant_used']) ? $bio['disinfectant_used'] : ''; ?>" placeholder="Product name">
            </div>
            <div class="form-group">
                <label>Houses Cleaned?</label>
                <select name="houses_cleaned">
                    <option value="0" <?php echo (isset($bio['houses_cleaned']) && $bio['houses_cleaned'] == 0) ? 'selected' : ''; ?>>No</option>
                    <option value="1" <?php echo (isset($bio['houses_cleaned']) && $bio['houses_cleaned'] == 1) ? 'selected' : ''; ?>>Yes</option>
                </select>
            </div>
            <div class="form-group">
                <label>Footbath Maintained?</label>
                <select name="footbath_maintained">
                    <option value="0" <?php echo (isset($bio['footbath_maintained']) && $bio['footbath_maintained'] == 0) ? 'selected' : ''; ?>>No</option>
                    <option value="1" <?php echo (isset($bio['footbath_maintained']) && $bio['footbath_maintained'] == 1) ? 'selected' : ''; ?>>Yes</option>
                </select>
            </div>
            <div class="form-group">
                <label>Rodent Control Done?</label>
                <select name="rodent_control">
                    <option value="0" <?php echo (isset($bio['rodent_control']) && $bio['rodent_control'] == 0) ? 'selected' : ''; ?>>No</option>
                    <option value="1" <?php echo (isset($bio['rodent_control']) && $bio['rodent_control'] == 1) ? 'selected' : ''; ?>>Yes</option>
                </select>
            </div>
            <div class="form-group">
                <label>Protective Gear Used?</label>
                <select name="protective_gear_used">
                    <option value="1" <?php echo (isset($bio['protective_gear_used']) && $bio['protective_gear_used'] == 1) ? 'selected' : ''; ?>>Yes</option>
                    <option value="0" <?php echo (isset($bio['protective_gear_used']) && $bio['protective_gear_used'] == 0) ? 'selected' : ''; ?>>No</option>
                </select>
            </div>
            <div class="form-group" style="grid-column: span 2;">
                <label>Visitors Recorded</label>
                <input type="text" name="visitors_recorded" value="<?php echo isset($bio['visitors_recorded']) ? $bio['visitors_recorded'] : ''; ?>" placeholder="Names / None">
            </div>
        </div>
    </div>
</div>
