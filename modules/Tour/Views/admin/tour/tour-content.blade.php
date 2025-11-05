<div class="panel">
    <div class="panel-title"><strong>{{__("Tour Content")}}</strong></div>
    <div class="panel-body">
        <div class="form-group " data-id="title" data-type="title">
            <label class="control-label">{{__("Title")}}</label>
            <input type="text" value="{{old('title',$translation->title)}}" placeholder="{{__("Title")}}" name="title" class="form-control">
        </div>
        <div class="form-group" data-id="content" data-type="content">
            <label class="control-label">{{__("Content")}}</label>
            <div class="">
                <textarea name="content" class="d-none has-ckeditor" id="content" cols="30" rows="10">{{old('content',$translation->content)}}</textarea>
            </div>
        </div>
        <div class="form-group d-none">
            <label class="control-label">{{__("Description")}}</label>
            <div class="">
                <textarea name="short_desc" class="form-control" cols="30" rows="4">{{old('short_desc',$translation->short_desc)}}</textarea>
            </div>
        </div>
        @if(is_default_lang())
          
           


            <div class="row">
                <div class="col-lg-6">
                    <div class="form-group">
                        <label class="control-label">{{__("Tour Date")}}</label>
                        <input type="date" name="tour_date" class="form-control " value="{{old('tour_date',$row->tour_date)}}" placeholder="{{__("Tour Date")}}">
                    </div>
                </div>
                
                
                <div class="col-lg-6">
                    <div class="form-group">
                        <label class="control-label">{{__("Category")}}</label>
                        <div class="">
                            <select name="category_id" class="form-control">
                                <option value="">{{__("-- Please Select --")}}</option>
                                <?php
                                $traverse = function ($categories, $prefix = '') use (&$traverse, $row) {
                                    foreach ($categories as $category) {
                                        $selected = '';
                                        if ($row->category_id == $category->id)
                                            $selected = 'selected';
                                        printf("<option value='%s' %s>%s</option>", $category->id, $selected, $prefix . ' ' . $category->name);
                                        $traverse($category->children, $prefix . '-');
                                    }
                                };
                                $traverse($tour_category);
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                <div class="form-group">
                    <label class="control-label">{{ __("Duration (in days)") }}</label>
                    <input type="number" name="duration" class="form-control" value="{{ old('duration', $row->duration) }}" placeholder="{{ __("Enter duration in days") }}">
                </div>
            </div>
               
                
            </div>

        @endif
        <?php do_action(\Modules\Tour\Hook::FORM_AFTER_MAX_PEOPLE,$row) ?>
        <div class="form-group-item">
            <label class="control-label">{{__('FAQs')}}</label>
            <div class="g-items-header">
                <div class="row">
                    <div class="col-md-5">{{__("Title")}}</div>
                    <div class="col-md-5">{{__('Content')}}</div>
                    <div class="col-md-1"></div>
                </div>
            </div>
            <div class="g-items">
                @if(!empty($translation->faqs))
                    @php if(!is_array($translation->faqs)) $translation->faqs = json_decode(old('faqs',$translation->faqs)); @endphp
                    @foreach($translation->faqs as $key=>$faq)
                        <div class="item" data-number="{{$key}}">
                            <div class="row">
                                <div class="col-md-5">
                                    <input type="text" name="faqs[{{$key}}][title]" class="form-control" value="{{$faq['title']}}" placeholder="{{__('Eg: When and where does the tour end?')}}">
                                </div>
                                <div class="col-md-6">
                                    <textarea name="faqs[{{$key}}][content]" class="form-control full-h" placeholder="...">{{$faq['content']}}</textarea>
                                </div>
                                <div class="col-md-1">
                                    <span class="btn btn-danger btn-sm btn-remove-item"><i class="fa fa-trash"></i></span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
            <div class="text-right">
                <span class="btn btn-info btn-sm btn-add-item"><i class="icon ion-ios-add-circle-outline"></i> {{__('Add item')}}</span>
            </div>
            <div class="g-more hide">
                <div class="item" data-number="__number__">
                    <div class="row">
                        <div class="col-md-5">
                            <input type="text" __name__="faqs[__number__][title]" class="form-control" placeholder="{{__('Eg: When and where does the tour end?')}}">
                        </div>
                        <div class="col-md-6">
                            <textarea __name__="faqs[__number__][content]" class="form-control full-h" placeholder="..."></textarea>
                        </div>
                        <div class="col-md-1">
                            <span class="btn btn-danger btn-sm btn-remove-item"><i class="fa fa-trash"></i></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('Tour::admin/tour/include-exclude')
        @include('Tour::admin/tour/itinerary')
        @if(is_default_lang())
            <div class="form-group">
                <label class="control-label">{{__("Banner Image")}}</label>
                <div class="form-group-image">
                    {!! \Modules\Media\Helpers\FileHelper::fieldUpload('banner_image_id',old('banner_image_id',$row->banner_image_id)) !!}
                </div>
            </div>
            <div class="form-group">
                <label class="control-label">{{__("Gallery")}}</label>
                {!! \Modules\Media\Helpers\FileHelper::fieldGalleryUpload('gallery',old('gallery',$row->gallery)) !!}
            </div>
        @endif
    </div>
</div>
