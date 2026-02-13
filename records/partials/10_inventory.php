<!-- 10. WEEKLY INVENTORY CHECK -->
<div class="record-section">
    <div class="section-header">10. Weekly Inventory Check 📦</div>
    <div class="section-body">
        <div class="dashboard-grid">
            <div class="form-group">
                <label>Feed Remaining (Bags)</label>
                <input type="number" step="0.1" name="feed_store_bags" value="<?php echo isset($inventory['feed_store_bags']) ? $inventory['feed_store_bags'] : ''; ?>" placeholder="0.0">
            </div>
            <div class="form-group">
                <label>Reorder Needed?</label>
                <select name="reorder_needed">
                    <option value="0" <?php echo (isset($inventory['reorder_needed']) && $inventory['reorder_needed'] == 0) ? 'selected' : ''; ?>>No</option>
                    <option value="1" <?php echo (isset($inventory['reorder_needed']) && $inventory['reorder_needed'] == 1) ? 'selected' : ''; ?>>Yes</option>
                </select>
            </div>
            <div class="form-group">
                <label>Drugs Low in Stock</label>
                <input type="text" name="drug_store_low" value="<?php echo isset($inventory['drug_store_low']) ? $inventory['drug_store_low'] : ''; ?>" placeholder="List drugs...">
            </div>
            <div class="form-group">
                <label>Expired Drugs Removed?</label>
                <select name="expired_drugs_removed">
                    <option value="0" <?php echo (isset($inventory['expired_drugs_removed']) && $inventory['expired_drugs_removed'] == 0) ? 'selected' : ''; ?>>No</option>
                    <option value="1" <?php echo (isset($inventory['expired_drugs_removed']) && $inventory['expired_drugs_removed'] == 1) ? 'selected' : ''; ?>>Yes</option>
                </select>
            </div>
            <div class="form-group">
                <label>Egg Trays Balance</label>
                <input type="number" name="egg_trays_balance" value="<?php echo isset($inventory['egg_trays_balance']) ? $inventory['egg_trays_balance'] : ''; ?>" placeholder="0">
            </div>
             <div class="form-group">
                <label>Disinfectants Remaining</label>
                <input type="text" name="disinfectants_remaining" value="<?php echo isset($inventory['disinfectants_remaining']) ? $inventory['disinfectants_remaining'] : ''; ?>" placeholder="Quantity">
            </div>
             <div class="form-group">
                <label>Supplies to Restock</label>
                <input type="text" name="supplies_restock" value="<?php echo isset($inventory['supplies_restock']) ? $inventory['supplies_restock'] : ''; ?>" placeholder="List items...">
            </div>
        </div>
    </div>
</div>
