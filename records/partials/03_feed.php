<!-- 3. WEEKLY FEED RECORD -->
<div class="record-section">
    <div class="section-header">3. Weekly Feed Record 🍗</div>
    <div class="section-body">
        <div class="dashboard-grid">
            <div class="form-group">
                <label>Feed Type Used</label>
                <input type="text" name="feed_type" value="<?php echo isset($feed['feed_type']) ? $feed['feed_type'] : ''; ?>" placeholder="e.g. Layers Mash">
            </div>
            <div class="form-group">
                <label>Stock at Start (Bags)</label>
                <input type="number" step="0.1" name="stock_start" value="<?php echo isset($feed['stock_start']) ? $feed['stock_start'] : ''; ?>" placeholder="0.0">
            </div>
            <div class="form-group">
                <label>Purchased (Bags)</label>
                <input type="number" step="0.1" name="purchased" value="<?php echo isset($feed['purchased']) ? $feed['purchased'] : ''; ?>" placeholder="0.0">
            </div>
             <div class="form-group">
                <label>Cost of Feed Purchased</label>
                <input type="number" step="0.01" name="cost_purchased" value="<?php echo isset($feed['cost_purchased']) ? $feed['cost_purchased'] : ''; ?>" placeholder="0.00">
            </div>
            <div class="form-group">
                <label>Used During Week (Bags)</label>
                <input type="number" step="0.1" name="used_bags" value="<?php echo isset($feed['used_bags']) ? $feed['used_bags'] : ''; ?>" placeholder="0.0">
            </div>
            <!-- 'Remaining' and 'Total Available' can be calculated, but manual input verifies stock check -->
            <div class="form-group">
                <label>Stock Remaining (End of Week)</label>
                <input type="number" step="0.1" name="stock_end" value="<?php echo isset($feed['stock_end']) ? $feed['stock_end'] : ''; ?>" placeholder="0.0">
            </div>
        </div>
    </div>
</div>
