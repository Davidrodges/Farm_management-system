<!-- 2. WEEKLY EGG PRODUCTION -->
<div class="record-section">
    <div class="section-header">2. Weekly Egg Production 🥚</div>
    <div class="section-body">
        <div class="dashboard-grid">
            <div class="form-group">
                <label>Total Eggs Collected (Pieces)</label>
                <input type="number" name="total_collected" value="<?php echo isset($eggs['total_collected']) ? $eggs['total_collected'] : ''; ?>" placeholder="0">
            </div>
            <div class="form-group">
                <label>Cracked / Broken</label>
                <input type="number" name="cracked_broken" value="<?php echo isset($eggs['cracked_broken']) ? $eggs['cracked_broken'] : ''; ?>" placeholder="0">
            </div>
            <div class="form-group">
                <label>Small Eggs</label>
                <input type="number" name="small_eggs" value="<?php echo isset($eggs['small_eggs']) ? $eggs['small_eggs'] : ''; ?>" placeholder="0">
            </div>
            <div class="form-group">
                <label>Fed to Dogs</label>
                <input type="number" name="fed_to_dogs" value="<?php echo isset($eggs['fed_to_dogs']) ? $eggs['fed_to_dogs'] : ''; ?>" placeholder="0">
            </div>
            <div class="form-group">
                <label>Consumed at Home</label>
                <input type="number" name="consumed_home" value="<?php echo isset($eggs['consumed_home']) ? $eggs['consumed_home'] : ''; ?>" placeholder="0">
            </div>
            <div class="form-group">
                <label>Discarded</label>
                <input type="number" name="discarded" value="<?php echo isset($eggs['discarded']) ? $eggs['discarded'] : ''; ?>" placeholder="0">
            </div>
            <!-- Calculated fields like 'Saleable' or 'Crates' can be done in JS or Backend, 
                 but user might want to input 'Crates Produced' manually if they pack them. -->
            <div class="form-group">
                <label>Crates Produced (Packed)</label>
                <input type="number" step="0.1" name="crates_produced" value="<?php echo isset($eggs['crates_produced']) ? $eggs['crates_produced'] : ''; ?>" placeholder="0.0">
            </div>
        </div>
    </div>
</div>
