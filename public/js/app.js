// Ad Form Config Array Collection
$('#add_picture').click(function(){
    const pictures = $('#ad_pictures');
    const widgetsCounter = $('#widgets-counter');
    const index = +widgetsCounter.val();
    const template = pictures.data('prototype').replace(/__name__/g, index);
    widgetsCounter.val(index + 1);
    pictures.append(template);
    handleDeleteButtons();
});

function handleDeleteButtons() {
    $('button[data-action="delete"]').click(function(){
        const target = this.dataset.target;
        $(target).remove();
    });
}

function updateCounter() {
    const widgetsCounter = $('#widgets-counter');
    const count = +$('#ad_pictures div.form-group').length;
    widgetsCounter.val(count);
}

updateCounter();
handleDeleteButtons();