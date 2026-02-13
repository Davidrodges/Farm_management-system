<!-- 7. WEEKLY LABOUR RECORD -->
<div class="record-section">
    <div class="section-header">7. Weekly Labour Record 👨‍🌾</div>
    <div class="section-body">
        <div class="dashboard-grid">
            <div class="form-group">
                <label>Number of Workers</label>
                <input type="number" name="num_workers" value="<?php echo isset($labour['num_workers']) ? $labour['num_workers'] : ''; ?>" placeholder="0">
            </div>
            <div class="form-group">
                <label>Days Worked per Worker</label>
                <input type="number" step="0.5" name="days_worked_per_worker" value="<?php echo isset($labour['days_worked_per_worker']) ? $labour['days_worked_per_worker'] : ''; ?>" placeholder="e.g. 6">
            </div>
            <div class="form-group">
                <label>Wages Paid (Total)</label>
                <input type="number" step="0.01" name="wages_paid" value="<?php echo isset($labour['wages_paid']) ? $labour['wages_paid'] : ''; ?>" placeholder="0.00">
            </div>
            <div class="form-group">
                <label>Casual Labour Hired (Cost)</label>
                <input type="number" step="0.01" name="casual_labour_cost" value="<?php echo isset($labour['casual_labour_cost']) ? $labour['casual_labour_cost'] : ''; ?>" placeholder="0.00">
            </div>
            <div class="form-group">
                <label>Workers' Meals Cost</label>
                <input type="number" step="0.01" name="meals_cost" value="<?php echo isset($labour['meals_cost']) ? $labour['meals_cost'] : ''; ?>" placeholder="0.00">
            </div>
            <div class="form-group" style="grid-column: span 2;">
                <label>Notes on Staff Performance</label>
                <textarea name="labour_notes" placeholder="Issues or commendations..."><?php echo isset($labour['notes']) ? $labour['notes'] : ''; ?></textarea>
            </div>
        </div>
    </div>
</div>
