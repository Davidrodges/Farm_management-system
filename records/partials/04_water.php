<!-- 4. WEEKLY WATER RECORD -->
<div class="record-section">
    <div class="section-header">4. Weekly Water Record 💧</div>
    <div class="section-body">
        <div class="dashboard-grid">
            <div class="form-group">
                <label>Water Source</label>
                <input type="text" name="water_source" value="<?php echo isset($water['water_source']) ? $water['water_source'] : ''; ?>" placeholder="e.g. Borehole">
            </div>
            <div class="form-group">
                <label>Treatments Given</label>
                <input type="text" name="treatments_given" value="<?php echo isset($water['treatments_given']) ? $water['treatments_given'] : ''; ?>" placeholder="e.g. Chlorine">
            </div>
             <div class="form-group">
                <label>Cost of Treatments</label>
                <input type="number" step="0.01" name="cost_treatments" value="<?php echo isset($water['cost_treatments']) ? $water['cost_treatments'] : ''; ?>" placeholder="0.00">
            </div>
            <div class="form-group" style="grid-column: span 2;">
                <label>Repairs / System Notes</label>
                <input type="text" name="water_repairs_notes" value="<?php echo isset($water['repairs_done']) ? $water['repairs_done'] : ''; ?>" placeholder="Any repairs or issues?">
            </div>
        </div>
    </div>
</div>
