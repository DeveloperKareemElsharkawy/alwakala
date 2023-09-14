var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl)
});

$('.select-all').click(function (event) {
    if (this.checked) {
        // Iterate each checkbox
        $(':checkbox').each(function () {
            this.checked = true;
        });
    } else {
        $(':checkbox').each(function () {
            this.checked = false;
        });
    }
});

function readURL(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            $('#imagePreview').css('background-image', 'url(' + e.target.result + ')');
            $('#imagePreview').hide();
            $('#imagePreview').fadeIn(650);
        }
        reader.readAsDataURL(input.files[0]);
    }
}

$("#imageUpload").change(function () {
    readURL(this);
});


function readURL2(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            $('#imagePreview2').css('background-image', 'url(' + e.target.result + ')');
            $('#imagePreview2').hide();
            $('#imagePreview2').fadeIn(650);
        }
        reader.readAsDataURL(input.files[0]);
    }
}

$("#imageUpload2").change(function () {
    readURL2(this);
});

function readURL3(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            $('#imagePreview3').css('background-image', 'url(' + e.target.result + ')');
            $('#imagePreview3').hide();
            $('#imagePreview3').fadeIn(650);
        }
        reader.readAsDataURL(input.files[0]);
    }
}

$("#imageUpload3").change(function () {
    readURL3(this);
});

$(document).ready(function () {
    $(".select-modal").each(function () {
        $(this)
            .select2({
                dropdownParent: $(this).parent()
            });
    });
    $('.select').select2({});
});


function initImageUpload(box) {
    let uploadField = box.querySelector('.image-upload');

    uploadField.addEventListener('change', getFile);

    function getFile(e) {
        let file = e.currentTarget.files[0];
        checkType(file);
    }

    function previewImage(file) {
        let thumb = box.querySelector('.js--image-preview'),
            reader = new FileReader();

        reader.onload = function () {
            thumb.style.backgroundImage = 'url(' + reader.result + ')';
        }
        reader.readAsDataURL(file);
        thumb.className += ' js--no-default';
    }

    function checkType(file) {
        let imageType = /image.*/;
        if (!file.type.match(imageType)) {
            throw 'Datei ist kein Bild';
        } else if (!file) {
            throw 'Kein Bild gewählt';
        } else {
            previewImage(file);
        }
    }

}

// initialize box-scope
var boxes = document.querySelectorAll('.box');

for (let i = 0; i < boxes.length; i++) {
    let box = boxes[i];
    initDropEffect(box);
    initImageUpload(box);
}


/// drop-effect
function initDropEffect(box) {
    let area, drop, areaWidth, areaHeight, maxDistance, dropWidth, dropHeight, x, y;

    // get clickable area for drop effect
    area = box.querySelector('.js--image-preview');
    area.addEventListener('click', fireRipple);

    function fireRipple(e) {
        area = e.currentTarget
        // create drop
        if (!drop) {
            drop = document.createElement('span');
            drop.className = 'drop';
            this.appendChild(drop);
        }
        // reset animate class
        drop.className = 'drop';

        // calculate dimensions of area (longest side)
        areaWidth = getComputedStyle(this, null).getPropertyValue("width");
        areaHeight = getComputedStyle(this, null).getPropertyValue("height");
        maxDistance = Math.max(parseInt(areaWidth, 10), parseInt(areaHeight, 10));

        // set drop dimensions to fill area
        drop.style.width = maxDistance + 'px';
        drop.style.height = maxDistance + 'px';

        // calculate dimensions of drop
        dropWidth = getComputedStyle(this, null).getPropertyValue("width");
        dropHeight = getComputedStyle(this, null).getPropertyValue("height");

        // calculate relative coordinates of click
        // logic: click coordinates relative to page - parent's position relative to page - half of self height/width to make it controllable from the center
        x = e.pageX - this.offsetLeft - (parseInt(dropWidth, 10) / 2);
        y = e.pageY - this.offsetTop - (parseInt(dropHeight, 10) / 2) - 30;

        // position drop and animate
        drop.style.top = y + 'px';
        drop.style.left = x + 'px';
        drop.className += ' animate';
        e.stopPropagation();

    }
}

$(function () {
    var selectedClass = "";
    $(".fil-cat").click(function () {
        $(".fil-cat").removeClass("selected");
        $(this).addClass("selected");
        selectedClass = $(this).attr("data-rel");
        $("#portfolio").fadeTo(100, 0.1);
        $("#portfolio tr").not("." + selectedClass).fadeOut().removeClass('scale-anm');
        setTimeout(function () {
            $("." + selectedClass).fadeIn().addClass('scale-anm');
            $("#portfolio").fadeTo(300, 1);
        }, 300);
    });
});
$('.product_link').hover(function () {
    $(this).parent().parent().parent().find('img').toggleClass('hover');
});
$(".card .dropdown-menu").click(function (e) {
    e.stopPropagation();
});


$(".extend_color").click(function (e) {
    e.preventDefault();
    $($(this).attr('classo')).append('<div><input class="colorSelector" type="color"><div class="result"></div><input type="number" value="0"><a class="remove-extend-field"><i class="bi bi-trash"></i></a></div>');
});

$(".extend_size").click(function (e) {
    e.preventDefault();
    $($(this).attr('classo')).append('<div><div class="gradient-box"><input type="text" value="xl"></div><div class="result"></div><input type="number" value="0"><a class="remove-extend-field"><i class="bi bi-trash"></i></a></div>');
});

$(".add_div").on("click", ".remove-extend-field", function (e) {
    e.preventDefault();
    $(this).parent('div').remove();
});

$(document).on("change", ".colorSelector", function () {
    $(this).parent().find('.result').html($(this).val());
});
$('.input').keypress(function (e) {
    if (e.which == 13) {
        $('form').submit();
        return false;    //<---- Add this line
    }
});

$(".copy").click(function () {
    Swal.fire({
        title: "تم النسخ بنجاح",
        timer: 1000,
        showCloseButton: false,
        showCancelButton: false,
        showConfirmButton: false
    });
    $(this).parent().find('input').select();
    document.execCommand('copy');
});

$('.product_delete').click(function () {
    $(this).toggleClass('active_linkk');
    $('.store_delete').removeClass('active_linkk');
    $('.product-input').slideToggle();
    $('.store-input').hide();
    $('.reason').hide();
    $('.product-input input').prop('checked', false);
    $('.store-input input').prop('checked', false);
});
$('.store_delete').click(function () {
    $(this).toggleClass('active_linkk');
    $('.product_delete').removeClass('active_linkk');
    $('.store-input').slideToggle();
    $('.product-input').hide();
    $('.reason').hide();
    $('.product-input input').prop('checked', false);
    $('.store-input input').prop('checked', false);
});

$('.product-input>input').change(function () {
    if ($(this).prop("checked") == 1) {
        $(this).parent().parent().find('.reason').show();
    } else {
        $(this).parent().parent().find('.reason').hide();
    }
});
$('.store-input>input').change(function () {
    if ($(this).prop("checked") == 1) {
        $(this).parent().parent().parent().parent().parent().parent().find('.store_reason').show();
    } else {
        $(this).parent().parent().parent().parent().parent().parent().find('.store_reason').hide();
    }
});

$('.nexttab').click(function () {
    $('.modal-dialog').addClass('modal-xl');
    $('.modal-dialog').removeClass('modal-lg');
});

$('input[name=store_id]').change(function () {
    if ($(this).prop("checked") == 1) {
        $(this).parent().find('label').html('turn on');
    }else{
        $(this).parent().find('label').html('turn off');
    }
});


$(window).on("load", function () {
    $('#elmLoader').fadeOut();
    $('.chat-conversation-list').fadeIn();
});

$('.chat-menu .nav-link').click(function () {
    $(".chat-leftsidebar").show();
});

$('.wekala').click(function () {
    $(".chat-leftsidebar").hide();
});

if($("#pills-callsss.show").length > 0){
    $(".chat-leftsidebar").hide();
}else{
    $(".chat-leftsidebar").show();
}

$('.points-page .store-link').hover(function () {
    $(this).parent().parent().addClass('shadow');
}, function () {
    $(this).parent().parent().removeClass('shadow');
});

$('.icon-video').click(function(){
    var video = $(this).parent().find('video').get(0);
    if (video.paused === false) {
        video.pause();
        $(this).css("opacity" , 1);
    } else {
        video.play();
        $(this).css("opacity" , 0);

    }
    return false;
});
$("video").on("ended", function() {
    var icon = $(this).parent().find('.icon-video').css("opacity" , 1);
});