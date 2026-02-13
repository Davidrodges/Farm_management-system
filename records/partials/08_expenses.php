<!-- 8. WEEKLY EXPENSES -->
<div class="record-section">
    <div class="section-header">8. Weekly Expenses 🧾</div>
    <div class="section-body">
        <p style="font-size: 0.9em; color: #666; margin-bottom: 1rem;">Note: Some costs are entered in previous sections. You can re-enter or summarize here.</p>
        <div class="dashboard-grid">
            <div class="form-group">
                <label>Feed Costs</label>
                <input type="number" step="0.01" name="feed_cost" value="<?php echo isset($expenses['feed_cost']) ? $expenses['feed_cost'] : ''; ?>" placeholder="0.00">
            </div>
            <div class="form-group">
                <label>Medicine & Vaccine</label>
                <input type="number" step="0.01" name="medicine_cost" value="<?php echo isset($expenses['medicine_cost']) ? $expenses['medicine_cost'] : ''; ?>" placeholder="0.00">
            </div>
            <div class="form-group">
                <label>Fuel (Vehicle/Pump/Gen)</label>
                <input type="number" step="0.01" name="fuel_cost" value="<?php echo isset($expenses['fuel_cost']) ? $expenses['fuel_cost'] : ''; ?>" placeholder="0.00">
            </div>
            <div class="form-group">
                <label>Repairs & Maintenance</label>
                <input type="number" step="0.01" name="repairs_cost" value="<?php echo isset($expenses['repairs_cost']) ? $expenses['repairs_cost'] : ''; ?>" placeholder="0.00">
            </div>
            <div class="form-group">
                <label>Electricity</label>
                <input type="number" step="0.01" name="electricity_cost" value="<?php echo isset($expenses['electricity_cost']) ? $expenses['electricity_cost'] : ''; ?>" placeholder="0.00">
            </div>
            <div class="form-group">
                <label>Water (Bill/Cost)</label>
                <input type="number" step="0.01" name="water_cost" value="<?php echo isset($expenses['water_cost']) ? $expenses['water_cost'] : ''; ?>" placeholder="0.00">
            </div>
            <div class="form-group">
                <label>Labour Wages</label>
                <input type="number" step="0.01" name="labour_cost" value="<?php echo isset($expenses['labour_cost']) ? $expenses['labour_cost'] : ''; ?>" placeholder="0.00">
            </div>
             <div class="form-group">
                <label>Transport</label>
                <input type="number" step="0.01" name="transport_cost" value="<?php echo isset($expenses['transport_cost']) ? $expenses['transport_cost'] : ''; ?>" placeholder="0.00">
            </div>
             <div class="form-group">
                <label>Farm Supplies</label>
                <input type="number" step="0.01" name="supplies_cost" value="<?php echo isset($expenses['supplies_cost']) ? $expenses['supplies_cost'] : ''; ?>" placeholder="0.00">
            </div>
             <div class="form-group">
                <label>Miscellaneous</label>
                <input type="number" step="0.01" name="misc_cost" value="<?php echo isset($expenses['misc_cost']) ? $expenses['misc_cost'] : ''; ?>" placeholder="0.00">
            </div>
        </div>
    </div>
</div>
