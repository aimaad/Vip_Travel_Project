(function ($) {
    new Vue({
        el:'#bravo_tour_book_app',
        data:{
            id:'',
            extra_price:[],
            person_types:[],
            message:{
                content:'',
                type:false
            },
            html:'',
            onSubmit:false,
            start_date:'',
            start_date_html:'',
            step:1,
            guests:1,
            price:0,
            total_price_before_fee:0,
            total_price_fee:0,
            max_guests:1,
            start_date_obj:'',
            duration:0,
            allEvents:[],
            buyer_fees:[],
            discount_by_people:[],
            discount_by_people_output:[],

            is_form_enquiry_and_book:false,
            enquiry_type:'book',
            enquiry_is_submit:false,
            enquiry_name:"",
            enquiry_email:"",
            enquiry_phone:"",
            enquiry_note:"",
        },
        watch:{
            extra_price:{
                handler:function f() {
                    this.step = 1;
                },
                deep:true
            },
          
            guests(){
                this.step = 1;
            },
            person_types:{
                handler:function f() {
                    this.step = 1;
                },
                deep:true
            },
           
        },
        computed: {
            total_price() {
                var me = this;
                console.log(me);
                if (me.start_date !== "") {
                    var total = 0;
                    var total_guests = 0;
                    var startDate = new Date(me.start_date).getTime();
                    
                    // Check if person_types is not null or undefined before looping through it
                    if (Array.isArray(me.person_types)) {
                        me.person_types.forEach(function(person_type) {
                            total += parseFloat(person_type.price) * parseInt(person_type.number);
                        });
                    } else {
                        total_guests = me.guests;
                        total += me.guests * me.price;
                    }
        
                    if (Array.isArray(me.extra_price)) {
                        me.extra_price.forEach(function(item) {
                            if (!item.price) return;
                            var type_total = 0;
                            if (item.enable == 1) {
                                switch (item.type) {
                                    case "one_time":
                                        type_total += parseFloat(item.price);
                                        break;
                                    case "per_hour":
                                        if (me.duration > 0) {
                                            type_total += parseFloat(item.price) * parseFloat(me.duration);
                                        }
                                        break;
                                    case "per_day":
                                        if (me.duration > 0) {
                                            type_total += parseFloat(item.price) * Math.ceil(parseFloat(me.duration) / 24);
                                        }
                                        break;
                                }
                                if (typeof item.per_person !== "undefined") {
                                    type_total = type_total * total_guests;
                                }
                                total += type_total;
                            }
                        });
                    }
        
                    let discount_by_people = [];
                    if (Array.isArray(me.discount_by_people)) {
                        me.discount_by_people.forEach(function(type) {
                            if (type.from <= total_guests && (!type.to || type.to >= total_guests)) {
                                let type_total = 0;
                                switch (type.type) {
                                    case "fixed":
                                        type_total = type.amount;
                                        break;
                                    case "percent":
                                        type_total = total / 100 * type.amount;
                                        break;
                                }
                                total -= type_total;
                                type.total = type_total;
                                discount_by_people.push(type);
                            }
                        });
                    }
                    me.discount_by_people_output = discount_by_people;
        
                    this.total_price_before_fee = total;
        
                    var total_fee = 0;
                    if (Array.isArray(me.buyer_fees)) {
                        me.buyer_fees.forEach(function(item) {
                            if (!item.price) return;
        
                            // for Fixed
                            var fee_price = parseFloat(item.price);
        
                            // for Percent
                            if (typeof item.unit !== "undefined" && item.unit === "percent") {
                                fee_price = (total / 100) * fee_price;
                            }
        
                            if (typeof item.per_person !== "undefined") {
                                fee_price = fee_price * total_guests;
                            }
                            total_fee += fee_price;
                        });
                    }
                    total += total_fee;
                    this.total_price_fee = total_fee;
        
                    return total;
                    
                }
                
                return 0;
            },
            
            // Define the total_price_html computed property
            total_price_html() {
                if (!this.total_price) return '';
                return window.bravo_format_money(this.total_price);
            },
            is_deposit_ready() {
                // Check if deposit and deposit_amount are defined and return true or false accordingly
                return this.deposit && this.deposit_amount;
            }
        }
        
        ,
        created:function(){
            for(var k in bravo_booking_data){
                this[k] = bravo_booking_data[k];
            }
        },
        mounted(){
            const appElement = document.getElementById('bravo_tour_book_app');
            this.start_date = appElement.getAttribute('data-tour-date');
            this.start_date_html = appElement.getAttribute('data-tour-date-html');
    
            // Log start_date to the console to check its value
            console.log('Start Date:', this.start_date); 
        },
        
        methods:{
            handleTotalPrice: function () {
            },
            fetchEvents(start,end){
                var me = this;
                var data = {
                    start: start.format('YYYY-MM-DD'),
                    end: end.format('YYYY-MM-DD'),
                    id:bravo_booking_data.id,
                    for_single:1
                };
                console.log(data);

                $.ajax({
                    url: bravo_booking_i18n.load_dates_url,
                    dataType:"json",
                    type:'get',
                    data:data,
                    beforeSend: function() {
                        $('.daterangepicker').addClass("loading");
                    },
                    success:function (json) {
                        me.allEvents = json;
                        var drp = $(me.$refs.start_date).data('daterangepicker');
                        drp.allEvents = json;
                        drp.renderCalendar('left');
                        if (!drp.singleDatePicker) {
                            drp.renderCalendar('right');
                        }
                        $('.daterangepicker').removeClass("loading");
                    },
                    error:function (e) {
                        console.log(e);
                        console.log("Can not get availability");
                    }
                });
            },
            formatMoney: function (m) {
                return window.bravo_format_money(m);
            },
            validate(){
                // Assuming the static start_date is always valid, skip this check
                this.message.status = true; 
                this.message.content = '';
                return true;
            },
            
            addPersonType(type) {
                console.log('Adding person type:', type);
                type.number = parseInt(type.number);
                if (type.number < parseInt(type.max) || !type.max) {
                    type.number += 1;
                }
                console.log('Updated number:', type.number);
            },
            minusPersonType(type) {
                console.log('Removing person type:', type);
                type.number = parseInt(type.number);
                if (type.number > type.min) {
                    type.number -= 1;
                }
                console.log('Updated number:', type.number);

            },
            changePersonType(type) {
                type.number = parseInt(type.number);
                if (type.number > parseInt(type.max)) {
                    type.number = type.max;
                }
                if (type.number < type.min) {
                    type.number = type.min;
                }
            }
        ,
            addGuestsType(){
                var me = this;
                if(me.guests < parseInt(me.max_guests) || !me.max_guests) me.guests +=1;
            },
            minusGuestsType(){
                var me = this;
                if(me.guests > 1) me.guests -=1;
            },

           
            
            doSubmit: function (e) {
                e.preventDefault();
                if (this.onSubmit) return false;
            
                var me = this;
                this.onSubmit = true;
            
                // Removed checkAvailability phase
                // Instead of checking availability, directly proceed with validation
            
                if (!me.validate()) {
                    me.onSubmit = false;
                    return;
                }
            
                me.message.content = '';
            
                if (me.step == 1) {
                    me.html = '';
                }
            
                $.ajax({
                    url: bookingCore.url + '/booking/addToCart',
                    data: {
                        service_id: me.id,
                        service_type: 'tour',
                        start_date: me.start_date,
                        person_types: me.person_types,
                        extra_price: me.extra_price,
                        guests: me.guests
                    },
                    dataType: 'json',
                    type: 'post',
                    success: function (res) {
            
                        if (!res.status) {
                            me.onSubmit = false;
                        }
                        if (res.message) {
                            me.message.content = res.message;
                            me.message.type = res.status;
                        }
            
                        if (res.step) {
                            me.step = res.step;
                        }
                        if (res.html) {
                            me.html = res.html;
                        }
            
                        if (res.url) {
                            window.location.href = res.url;
                        }
            
                        if (res.errors && typeof res.errors == 'object') {
                            var html = '';
                            for (var i in res.errors) {
                                html += res.errors[i] + '<br>';
                            }
                            me.message.content = html;
                        }
                    },
                    error: function (e) {
                        console.log(e);
                        me.onSubmit = false;
            
                        bravo_handle_error_response(e);
            
                        if (e.status == 401) {
                            $('.bravo_single_book_wrap').modal('hide');
                        }
            
                        if (e.status != 401 && e.responseJSON) {
                            me.message.content = e.responseJSON.message ? e.responseJSON.message : 'Cannot book';
                            me.message.type = false;
                        }
                    }
                });
            },            
            
            doEnquirySubmit:function(e){
                e.preventDefault();
                if(this.onSubmit) return false;
                if(!this.validateenquiry()) return false;
                this.onSubmit = true;
                var me = this;
                this.message.content = '';

                $.ajax({
                    url:bookingCore.url+'/booking/addEnquiry',
                    data:{
                        service_id:this.id,
                        service_type:'tour',
                        name:this.enquiry_name,
                        email:this.enquiry_email,
                        phone:this.enquiry_phone,
                        note:this.enquiry_note,
                    },
                    dataType:'json',
                    type:'post',
                    success:function(res){
                        if(res.message)
                        {
                            me.message.content = res.message;
                            me.message.type = res.status;
                        }
                        if(res.errors && typeof res.errors == 'object')
                        {
                            var html = '';
                            for(var i in res.errors){
                                html += res.errors[i]+'<br>';
                            }
                            me.message.content = html;
                        }
                        if(res.status){
                            me.enquiry_is_submit = true;
                            me.enquiry_name = "";
                            me.enquiry_email = "";
                            me.enquiry_phone = "";
                            me.enquiry_note = "";
                        }
                        me.onSubmit = false;

                    },
                    error:function (e) {
                        me.onSubmit = false;
                        bravo_handle_error_response(e);
                        if(e.status == 401){
                            $('.bravo_single_book_wrap').modal('hide');
                        }
                        if(e.status != 401 && e.responseJSON){
                            me.message.content = e.responseJSON.message ? e.responseJSON.message : 'Can not booking';
                            me.message.type = false;
                        }
                    }
                })
            },
            validateenquiry(){
                if(!this.enquiry_name)
                {
                    this.message.status = false;
                    this.message.content = bravo_booking_i18n.name_required;
                    return false;
                }
                if(!this.enquiry_email)
                {
                    this.message.status = false;
                    this.message.content = bravo_booking_i18n.email_required;
                    return false;
                }
                return true;
            },
            openStartDate(){
                $(this.$refs.start_date).trigger('click');
            }
        }

    });


    $(window).on("load", function () {
        var urlHash = window.location.href.split("#")[1];
        if (urlHash &&  $('.' + urlHash).length ){
            var offset_other = 70
            if(urlHash === "review-list"){
                offset_other = 330;
            }
            $('html,body').animate({
                scrollTop: $('.' + urlHash).offset().top - offset_other
            }, 1000);
        }
    });

    $(".bravo-button-book-mobile").click(function () {
        $('.bravo_single_book_wrap').modal('show');
    });

    $(".bravo_detail_tour .g-faq .item .header").click(function () {
        $(this).parent().toggleClass("active");
    });


    $(".bravo_detail_tour .g-itinerary").each(function () {
        $(this).find(".owl-carousel").owlCarousel({
            items: 3,
            loop: false,
            margin: 15,
            nav: false,
            responsive: {
                0: {
                    items: 1
                },
                768: {
                    items: 2
                },
                1000: {
                    items: 3
                }
            }
        })
    });

})(jQuery);
