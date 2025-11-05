<div class="panel">
    <div class="panel-title"><strong>{{__('Publishing Options')}}</strong></div>
    <div class="panel-body">
        <!-- Publish Now Button -->
        <label class="radio-inline">
            <input type="radio" name="publish_option" value="publish_now" 
                @if($row->status == 'publish') checked @endif required>
            {{__('Publish Now')}}
        </label>

        <!-- Save as Draft Button -->
        <label class="radio-inline">
            <input type="radio" name="publish_option" value="save_draft" 
                @if($row->status == 'draft' || empty($row->status)) checked @endif required>
            {{__('Save as Draft')}}
        </label>

        <!-- Schedule Button -->
        <label class="radio-inline">
            <input type="radio" name="publish_option" value="schedule" 
                @if($row->status == 'scheduled') checked @endif required>
            {{__('Schedule')}}
        </label>

        <!-- Date pickers for scheduling (only visible when scheduling is selected) -->
        <div id="schedule-options" style="display: none; display: flex; gap: 15px; margin-top:50px !important;">
            <div class="form-group">
                <label>{{__('Publish Date')}}</label>
                <input type="date" class="form-control" name="publish_date" 
                    value="{{ old('publish_date', $row->publish_date ? \Carbon\Carbon::parse($row->publish_date)->format('Y-m-d') : '') }}" 
                    placeholder="{{ __('Select when the tour should be published') }}">
            </div>
            <div class="form-group">
                <label>{{__('Draft Date')}}</label>
                <input type="date" class="form-control" name="draft_date" 
                    value="{{ old('draft_date', $row->draft_date ? \Carbon\Carbon::parse($row->draft_date)->format('Y-m-d') : '') }}" 
                    placeholder="{{ __('Select when the tour should be drafted') }}">
            </div>
        </div>
    </div>
</div>



@foreach ($attributes as $attribute)
    @php $translate = $attribute->translate(app_get_locale()); @endphp
    <div class="panel">
        <div class="panel-title"><strong>{{__('Attribute: :name',['name'=>$translate->name])}}</strong></div>
        <div class="panel-body">
            <div class="terms-scrollable">
                @foreach($attribute->terms as $term)
                    @php $term_translate = $term->translate(app_get_locale()); @endphp
                    <label class="term-item">
                        <input @if(!empty($selected_terms) and $selected_terms->contains($term->id)) checked @endif type="checkbox" name="terms[]" value="{{$term->id}}">
                        <span class="term-name">{{$term_translate->name}}</span>
                    </label>
                @endforeach
            </div>
        </div>
    </div>
@endforeach

<!-- Include jQuery (make sure this is before any script that uses jQuery) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
   $(document).ready(function () {
    // Initial setup: hide or show schedule options based on selected option
    toggleScheduleOptions($('input[name="publish_option"]:checked').val());

    // Listen for changes in the publish_option radio buttons
    $('input[name="publish_option"]').on('change', function () {
        toggleScheduleOptions($(this).val());
    });

    function toggleScheduleOptions(option) {
        if (option === 'schedule') {
            $('#schedule-options').show();
            $('input[name="publish_date"], input[name="draft_date"]').prop('required', true);
        } else {
            $('#schedule-options').hide();
            $('input[name="publish_date"], input[name="draft_date"]').prop('required', false).val(''); // Reset values to null
        }
    }
});

 </script>
 
