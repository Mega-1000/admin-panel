$('#add_tag').on('click', e => {
    e.preventDefault();

    let content = $('#content').val();
    let selectedTag = $('#tag_select').val();

    if(!selectedTag) return false;

    content = `${content} \n${selectedTag}\n`;

    $('#content').val(content);
});
