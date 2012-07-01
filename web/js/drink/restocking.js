$(function () {
    // Get the div that holds the collection of lines
    var $collectionContainer = $('#restocking_lines');
    var collectionHolder = $('<ul></ul>').appendTo($collectionContainer);

    // setup an "add a line" link
    var $addLineLink = $('<a href="#" class="add_line_link">Add a line</a>');
    var $newLinkLi = $('<li></li>').append($addLineLink);

    // add the "add a line" anchor and li to the lines ul
    collectionHolder.append($newLinkLi);

    $addLineLink.on('click', function(e) {
        // prevent the link from creating a "#" on the URL
        e.preventDefault();

        // add a new line form (see next code block)
        addLineForm(collectionHolder, $newLinkLi);
    });

    function addLineForm(collectionHolder, $newLinkLi) {
        // Get the data-prototype we explained earlier
        var prototype = collectionHolder.parent().attr('data-prototype');

        // Replace '$$name$$' in the prototype's HTML to
        // instead be a number based on the current collection's length.
        var newForm = prototype.replace(/__name__/g, collectionHolder.children().length);

        // Display the form in the page in an li, before the "Add a line" link li
        var $newFormLi = $('<li></li>').append(newForm);
        $newLinkLi.before($newFormLi);
    }
});