<!-- 5. WEEKLY HEALTH RECORD -->
<div class="record-section">
    <div class="section-header">5. Weekly Health Record 💊</div>
    <div class="section-body">
        <div class="dashboard-grid">
            <div class="form-group">
                <label>Vaccinations (Name & Date)</label>
                <input type="text" name="vaccinations" value="<?php echo isset($health['vaccinations']) ? $health['vaccinations'] : ''; ?>" placeholder="Enter details">
            </div>
            <div class="form-group">
                <label>Diseases Observed</label>
                <input type="text" name="diseases_observed" value="<?php echo isset($health['diseases_observed']) ? $health['diseases_observed'] : ''; ?>" placeholder="Enter details">
            </div>
             <div class="form-group">
                <label>Symptoms Noticed</label>
                <input type="text" name="symptoms" value="<?php echo isset($health['symptoms']) ? $health['symptoms'] : ''; ?>" placeholder="Enter details">
            </div>
            <div class="form-group">
                <label>Drugs/Vitamins Given</label>
                <input type="text" name="drugs_given" value="<?php echo isset($health['drugs_given']) ? $health['drugs_given'] : ''; ?>" placeholder="Enter details">
            </div>
            <div class="form-group">
                <label>Treatment Duration</label>
                <input type="text" name="treatment_duration" value="<?php echo isset($health['treatment_duration']) ? $health['treatment_duration'] : ''; ?>" placeholder="e.g. 3 days">
            </div>
            <div class="form-group">
                <label>Withdrawal Periods Active</label>
                <input type="text" name="withdrawal_period" value="<?php echo isset($health['withdrawal_period']) ? $health['withdrawal_period'] : ''; ?>" placeholder="e.g. No sales until...">
            </div>
            <div class="form-group">
                <label>Vet Visits</label>
                <input type="text" name="vet_visits" value="<?php echo isset($health['vet_visits']) ? $health['vet_visits'] : ''; ?>" placeholder="Name/Date">
            </div>
            <div class="form-group">
                <label>Vet Recommendations</label>
                <textarea name="vet_recommendations" placeholder="Notes..."><?php echo isset($health['vet_recommendations']) ? $health['vet_recommendations'] : ''; ?></textarea>
            </div>
            <div class="form-group">
                <label>Deaths Linked to Illness</label>
                <input type="number" name="deaths_linked_illness" value="<?php echo isset($health['deaths_linked_illness']) ? $health['deaths_linked_illness'] : ''; ?>" placeholder="0">
            </div>
        </div>
    </div>
</div>
