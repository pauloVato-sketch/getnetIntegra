// register jQuery extension to select by TAB and Navation by press TAB
jQuery.extend(jQuery.expr[':'], {
    focusable: function(el) {
        return $(el).is('a, button, :input, [tabindex]');
    }
});

/**
 *
 */
(function() {

    var ZeedhiDirectives = angular.module('ZeedhiDirectives');
    ZeedhiDirectives.directive('zhSelectAutocomplete', ['$timeout', 'eventEngine', zhSelectAutocomplete]);

    function zhSelectAutocomplete($timeout, eventEngine) {

        var EDITABLE_INPUT_MIN_WIDTH = 130;
        var ENTER = 13;
        var SPACE = 32;
        var DOWN = 40;
        var ESC = 27;
        var TAB = 9;
        var UP = 38;


        var linkFunction = function(theScope, theElement) {

            var $scope = theScope;
            var element = theElement;
            var selectedList = '';
            var lastMouseDownElement;
            var loaderElement = $('img.autocomplete-loading-icon');
            var namespace = $scope.$id;
            var documentEvents = [];
            var lazyLoadEventIndex;

            $scope.hideLoader = function() {
                loaderElement.hide();
            };
            $scope.showLoader = function() {
                loaderElement.css('display', 'initial');
            };

            $scope.itemSearch = $scope.itemSearch || "";
            $scope.selectAutoCompleteElement = $(element);
            $scope.inputWithEditableContent = $(element).find('.editable');

            var initialPlaceholderValue;

            $scope.spanValueField = $(element).find('span');

            $scope.inputWithEditableContent.change(function(e) {
                e.stopPropagation();
            });

            $scope.field.element.click(function(e) {
                e.stopPropagation();
            });

            if($scope.field.lazyLoadEvent){
                lazyLoadEventIndex = eventEngine.bindEvent($scope.inputWithEditableContent, $scope.field.lazyLoadEvent, function(){
                    $scope.field.initializeField();
                });
            }

            /*
            *   Prevent loader from canceling element click
            */
            $('div.zh-background-loading').mouseup( function(e){
            	if(lastMouseDownElement && lastMouseDownElement === $scope.inputWithEditableContent[0]){
                    $scope.inputWithEditableContent.click();
                }
            });

            $(document).on('mousedown.'+namespace, function(e) {
                lastMouseDownElement = e.target;
            });
            documentEvents.push('mousedown');

            createFocusListener();
            clickListener();
            KeyPressNavegationListener();

            /**
             * Creates the click clickListener.
             *
             * @function clickListener
             * @private
             */
            function clickListener() {
                var showSelect = $(element).find(".show-select");
                showSelect.click(function(event) {
                    event.stopPropagation();
                });
            }

            /**
             * Resets the placeholder value.
             *
             * @function resetPlaceholderValue
             * @private
             */
            $scope.resetPlaceholderValue = function() {
                if(initialPlaceholderValue){
                    $scope.inputWithEditableContent[0].placeholder = initialPlaceholderValue;
                } else {
                    initialPlaceholderValue = $scope.inputWithEditableContent[0].placeholder;
                }
            };

            /**
             * Creates the focus listener.
             *
             * @function createFocusListener
             * @private
             */
            function createFocusListener() {
                $scope.inputWithEditableContent.focus(function(event) {
                    $scope.inputWithEditableContent.val($scope.itemSearch);
                    $scope.resetPlaceholderValue();
                    $scope.inputWithEditableContent.select();
                    $scope.inputWithEditableContent.parent().scrollTop(0);
                    $scope.showSpan = false;
                    $scope.changeDropdownListPosition();
                    $scope.$apply();
                    $scope.processBeforeSelectOpenEvent($scope.field);
                    $scope.openList();
                    $scope.updateDropdown();
                    $scope.processAfterSelectOpenEvent($scope.field);
                    $scope.selectAutoCompleteElement.closest("[zh-scroller]").on("scroll." + $scope.$id, function(){
                        $scope.hideList();
                    });
                });
            }

            /**
             * Updates the dropdown and selects the first item
             *
             * @function updateDropdown
             *
             * @returns {Array.<Object>} The selected items list.
             */
            $scope.updateDropdown = function(){
                if($scope.selectAutoCompleteElement.find('.selected').length === 0 && $scope.itemSearch){
                    var spanList = $scope.selectAutoCompleteElement.find('.show-select span');

                    selectedList = spanList.eq(0).addClass('selected');
                }

                return selectedList;
            };

            /**
             * Creates the key press navigation listener.
             *
             * @function KeyPressNavegationListener
             * @private
             */
            function KeyPressNavegationListener() {
                var next;
                var navegationKeyPress = $scope.selectAutoCompleteElement;
                navegationKeyPress.on('keydown', 'input,select', function(event) {

                    var EVENTKEY = event;
                    var spanList = $scope.selectAutoCompleteElement.find('.show-select span');

                    //Fix cursor input 'auto' to change position
                    if(EVENTKEY.which === DOWN || EVENTKEY.which === UP ) {
                        EVENTKEY.preventDefault();
                    }

                    //Enter, Space or Tab
                    if (EVENTKEY.which === ENTER || EVENTKEY.which === TAB) {
                        EVENTKEY.preventDefault();

                        if (isShowMore(selectedList)) {
                            $scope.loadMore(event);
                        }
                        else {
                            if($scope.shouldMakeRequest($scope.itemSearch)) {
                                $scope.selectAfterLoad();
                            } else if(canSelect(selectedList)){
                                $scope.selectItem(selectedList);
                                $scope.inputWithEditableContent.val("");
                            }
                            if (getFocusableItems().length > 1) {
                                $scope.removeFocusOfInput();

                                $scope.focusOnNextItem();
                                $scope.hideList();
                            }
                        }

                    }
                    //Down
                    else if (EVENTKEY.which === DOWN) {
                        if (selectedList) {
                            selectedList.removeClass('selected');
                            next = selectedList.next();
                            if (next.length > 0) {
                                selectedList = next.addClass('selected');
                            }
                            else {
                                selectedList = spanList.eq(0).addClass('selected');
                            }
                        }
                        else {
                            selectedList = spanList.eq(0).addClass('selected');
                        }
                    }
                    //Up
                    else if (EVENTKEY.which === UP) {
                        if (selectedList) {
                            selectedList.removeClass('selected');
                            next = selectedList.prev();
                            if (next.length > 0) {
                                selectedList = next.addClass('selected');
                            }
                            else {
                                selectedList = spanList.last().addClass('selected');
                            }
                        }
                        else {
                            selectedList = spanList.last().addClass('selected');
                        }
                    }
                    //Esc
                    else if (EVENTKEY.which === ESC) {
                        $scope.hideList();
                        selectedList = '';
                        return false;
                    }
                    if (navegationKeyPress) {
                        var itemList = $scope.selectAutoCompleteElement.find('.list-items');
                        var item = navegationKeyPress.find('.selected');
                        var index;

                        if (item.offset() !== undefined) {
                            if (EVENTKEY.which === ENTER) {
                                $timeout(function() {
                                    spanList = $scope.selectAutoCompleteElement.find('.show-select span');
                                    index = $scope.dropDownLimit - 10;
                                    movimentation = (index + 3.7) * 32 + item.outerHeight() - itemList.innerHeight();
                                    animateItemFocus(itemList, movimentation);
                                    selectedList = spanList.eq(index);
                                    selectedList.addClass('selected');
                                }, 1000, false);
                            }
                            else {
                                index = parseInt(item.attr('data-item-index')) || parseInt(item.prev().attr('data-item-index')) + 1;
                                var movimentation = 0;
                                if (item.position().top + item.outerHeight() > itemList.innerHeight()) {
                                    movimentation = (index * 32 + item.outerHeight() - itemList.innerHeight());
                                    animateItemFocus(itemList, movimentation);
                                }
                                else if (item.position().top < 0) {
                                    movimentation = index * 32;
                                    animateItemFocus(itemList, movimentation);
                                }
                            }
                        }
                    }
                });
            }

            /**
             * Makes the animation to scroll the item for the one who has focus.
             *
             * @function animateItemFocus
             * @private
             * @param  {Object} itemList      - The element witch has the list of items
             * @param  {Number} movimentation - The value in pixels of the scrolling distance from the top.
             */
            function animateItemFocus(itemList, movimentation) {
                itemList.finish().animate({
                    scrollTop: movimentation
                }, 500);
            }

            /**
             * Get all the focusabel items.
             *
             * @function getFocusableItems
             * @private
             * @returns {Array.<Object>} The focusable items on the screen.
             */
            function getFocusableItems() {
                return $(':focusable');
            }

            /**
             * Test if the given selectedList is a valid row to be selected.
             *
             * @function canSelect
             * @private
             * @returns {Boolean} Result telling if the selectedList is a valid row to be selected.
             */
            function canSelect(selectedList) {
                return (typeof selectedList === "string" && selectedList !== "") ||
                    (typeof selectedList === "object" && selectedList.text() !== "") &&
                    selectedList.attr("data-item-identifier");
            }

            function isShowMore(selectedItem) {
                return selectedItem && selectedItem.hasClass("view-more");
            }

            /**
             * Open the field's dropdown list.
             *
             * @function openList
             */
            $scope.openList = function() {
                $(".zh-select-autocomplete .show-select").css("display", "none");
                $scope.selectAutoCompleteElement.find(".show-select").css("display", "block");
                $scope.selectAutoCompleteElement.find('.list-items').scrollTop(0);
            };


            /**
             * Change the dropdown list position on field.
             *
             * @function changeDropdownListPosition
             */
            $scope.changeDropdownListPosition = function() {
                var dropdown = $scope.selectAutoCompleteElement.find('.zh-select-autocomplete');
                var dropdownOffset = dropdown.offset().top;
                var listItemsElement = $scope.selectAutoCompleteElement.find('.list-items');

                updateListWidth(listItemsElement, dropdown);
                updateListPosition(listItemsElement, dropdownOffset, dropdown);
            };

            /**
             * Updates the position of the dropdown list.
             *
             * @function updateListPosition
             * @param {Element} listItemsElement - The list's DOM.
             * @param {Element} dropdownOffset - The dropdown's offset.
             * @param {Element} dropdown - The dropdown's DOM.
             */
            function updateListPosition(listItemsElement, dropdownOffset, dropdown) {
                var popupModal = $('.popup-modal');
                if (popupModal.length) {
                    dropdownOffset -= popupModal.offset().top;
                }
                $timeout(function() {
                    listItemsElement.css('top', dropdownOffset + dropdown.outerHeight() - getParentFixedTop(dropdown));
                    if (dropdown.offset().top + dropdown.outerHeight() + listItemsElement.outerHeight() + 10 > $(window).height()) {
                        listItemsElement.css('margin-top', -1 * (dropdown.outerHeight() + listItemsElement.outerHeight()));
                    }
                    else {
                        listItemsElement.css('margin-top', 0);
                    }
                }, 0, false);
            }

            /**
             * Updates the width of the dropdown list.
             *
             * @function updateListPosition
             * @param {Element} listItemsElement - The list's DOM.
             * @param {Element} dropdown - The dropdown's DOM.
             */
            function updateListWidth(listItemsElement, dropdown) {
                //Set the correct width field
                if(listItemsElement.width() !== dropdown.innerWidth()){
                    listItemsElement.width(dropdown.innerWidth());
                }
            }

            /**
             * Get the parent fixed top position element.
             *
             * @function getParentFixedTop
             * @param {String} el - A element of the DOM.
             */
            function getParentFixedTop(el) {
                var parents = el.parents();
                var top = 0;
                parents.each(function(idx, item) {
                    item = $(item);
                    if ((item.css('position') === "fixed" || item.css('position') === "absolute") &&
                        item.parent() && (item.parent().css('position') === "fixed" || item.parent().css('position') === "absolute")) {
                        top = item.offset().top;
                        return false;
                    }
                });

                return top;
            }

            /**
             * Clear the field input's text.
             *
             * @function clearInputFieldText
             */
            $scope.clearInputFieldText = function() {
                $scope.selectAutoCompleteElement.find('input:text').val('');
            };

            /**
             * Hide the fields's dropdown list.
             *
             * @function hideList
             */
            $scope.hideList = function() {
                $scope.selectAutoCompleteElement.find(".show-select").hide();
                $scope.selectAutoCompleteElement.find('.show-select span').removeClass('selected');
                $scope.selectAutoCompleteElement.closest("[zh-scroller]").off("scroll." + $scope.$id);
            };

            /**
             * Set as selected the given item.
             *
             * @function selectItem
             * @param {Object} selectedList - The DOM element of the selected row.
             */
            $scope.selectItem = function(selectedList) {
                if (!selectedList) return;
                selectedItem = selectedList.text();
                var itemIdentifier = selectedList.attr("data-item-identifier");
                $scope.onAutoCompleteItemSelect(itemIdentifier);
            };

            /**
             * Focus on the next item of the screen.
             *
             * @function focusOnNextItem
             */
            $scope.focusOnNextItem = function() {
                $scope.inputHasFocus = false;
                $canfocus = getFocusableItems();
                var index = $canfocus.index(document.activeElement) + 1;
                if (index >= $canfocus.length) index = 0;
                $canfocus.eq(index).focus();
            };

            $scope.focusOnInput = function() {
                $timeout(function() {
                    $scope.inputWithEditableContent.focus();
                }, 0, false);
            };

            $scope.removeFocusOfInput = function() {

                $timeout(function() {
                    $scope.inputWithEditableContent.blur();

                    if($scope.itemSearch){
                        $scope.inputWithEditableContent[0].placeholder = $scope.itemSearch;
                    }

                    $scope.hideList();
                    $scope.showSpan = $scope.hasValueSetted();
                    $scope.clearInputFieldText();
                });
            };

            $scope.clearSelectedItem = function() {
                var item = $scope.inputWithEditableContent.find('.selected');
                selectedList = '';
                item.removeClass('selected');
            };

            /**
             * Treats the click outside the component.
             */
            $(document).on("click." + namespace, function(event) {
                if (!$.contains($scope.selectAutoCompleteElement[0], event.target)){
                    $scope.removeFocusOfInput();
                }
            });
            documentEvents.push('click');

            $scope.$on("$destroy", function(){
                documentEvents.forEach(function(eventName){
                    $(document).off(eventName + "." + namespace);
                });

                $('div.zh-background-loading').off('mouseup');
                $scope.selectAutoCompleteElement.closest("[zh-scroller]").off("scroll." + $scope.$id);
                eventEngine.unbindEvent($scope.inputWithEditableContent, $scope.field.lazyLoadEvent, lazyLoadEventIndex);
            });

        };
        return {
            restrict: 'A',
            link: linkFunction
        };
    }
})();