(function () {
    ("use strict");

    /**
     *  global variables
     */
    const navbarMenuElement = document.querySelector(".navbar-menu");
    const navbarMenuHTML = navbarMenuElement ? navbarMenuElement.innerHTML : "";
    const horizontalMenuSplit = 7; // after this number all horizontal menus will be moved in More menu options

    function pluginData() {
        /**
         * Common plugins
         */
        /**
         * Toast UI Notification
         */
        const toastExamples = document.querySelectorAll("[data-toast]");
        Array.from(toastExamples).forEach(function (element) {
            element.addEventListener("click", function () {
                const toastData = {};
                const isToastVal = element.attributes;
                if (isToastVal["data-toast-text"]) {
                    toastData.text = isToastVal["data-toast-text"].value.toString();
                }
                if (isToastVal["data-toast-gravity"]) {
                    toastData.gravity = isToastVal["data-toast-gravity"].value.toString();
                }
                if (isToastVal["data-toast-position"]) {
                    toastData.position = isToastVal["data-toast-position"].value.toString();
                }
                if (isToastVal["data-toast-className"]) {
                    toastData.className = isToastVal["data-toast-className"].value.toString();
                }
                if (isToastVal["data-toast-duration"]) {
                    toastData.duration = isToastVal["data-toast-duration"].value.toString();
                }
                if (isToastVal["data-toast-close"]) {
                    toastData.close = isToastVal["data-toast-close"].value.toString();
                }
                if (isToastVal["data-toast-style"]) {
                    toastData.style = isToastVal["data-toast-style"].value.toString();
                }
                if (isToastVal["data-toast-offset"]) {
                    toastData.offset = isToastVal["data-toast-offset"];
                }
                Toastify({
                    newWindow: true,
                    text: toastData.text,
                    gravity: toastData.gravity,
                    position: toastData.position,
                    className: "bg-" + toastData.className,
                    stopOnFocus: true,
                    offset: {
                        x: toastData.offset ? 50 : 0, // horizontal axis - can be a number or a string indicating unity. eg: '2em'
                        y: toastData.offset ? 10 : 0, // vertical axis - can be a number or a string indicating unity. eg: '2em'
                    },
                    duration: toastData.duration,
                    close: toastData.close === "close",
                    style: toastData.style === "style" ? {
                        background: "linear-gradient(to right, #0AB39C, #405189)"
                    } : "",
                }).showToast();
            });
        });

        /**
         * flatpickr
         */
        const flatpickrExamples = document.querySelectorAll("[data-provider]");
        Array.from(flatpickrExamples).forEach(function (item) {
            let dates;
            if (item.getAttribute("data-provider") === "flatpickr") {
                const dateData = {};
                const isFlatpickerVal = item.attributes;
                dateData.disableMobile = "true";
                if (isFlatpickerVal["data-date-format"])
                    dateData.dateFormat = isFlatpickerVal["data-date-format"].value.toString();
                if (isFlatpickerVal["data-enable-time"]) {
                    (dateData.enableTime = true),
                        (dateData.dateFormat = isFlatpickerVal["data-date-format"].value.toString() + " H:i");
                }
                if (isFlatpickerVal["data-altFormat"]) {
                    (dateData.altInput = true),
                        (dateData.altFormat = isFlatpickerVal["data-altFormat"].value.toString());
                }
                if (isFlatpickerVal["data-minDate"]) {
                    dateData.minDate = isFlatpickerVal["data-minDate"].value.toString();
                    dateData.dateFormat = isFlatpickerVal["data-date-format"].value.toString();
                }
                if (isFlatpickerVal["data-maxDate"]) {
                    dateData.maxDate = isFlatpickerVal["data-maxDate"].value.toString();
                    dateData.dateFormat = isFlatpickerVal["data-date-format"].value.toString();
                }
                if (isFlatpickerVal["data-deafult-date"]) {
                    dateData.defaultDate = isFlatpickerVal["data-deafult-date"].value.toString();
                    dateData.dateFormat = isFlatpickerVal["data-date-format"].value.toString();
                }
                if (isFlatpickerVal["data-multiple-date"]) {
                    dateData.mode = "multiple";
                    dateData.dateFormat = isFlatpickerVal["data-date-format"].value.toString();
                }
                if (isFlatpickerVal["data-range-date"]) {
                    dateData.mode = "range";
                    dateData.dateFormat = isFlatpickerVal["data-date-format"].value.toString();
                }
                if (isFlatpickerVal["data-inline-date"]) {
                    (dateData.inline = true),
                        (dateData.defaultDate = isFlatpickerVal["data-deafult-date"].value.toString());
                    dateData.dateFormat = isFlatpickerVal["data-date-format"].value.toString();
                }
                if (isFlatpickerVal["data-disable-date"]) {
                    dates = [];
                    dates.push(isFlatpickerVal["data-disable-date"].value);
                    dateData.disable = dates.toString().split(",");
                }
                if (isFlatpickerVal["data-week-number"]) {
                    dates = [];
                    dates.push(isFlatpickerVal["data-week-number"].value);
                    dateData.weekNumbers = true
                }
                flatpickr(item, dateData);
            } else if (item.getAttribute("data-provider") === "timepickr") {
                const timeData = {};
                const isTimepickerVal = item.attributes;
                if (isTimepickerVal["data-time-basic"]) {
                    (timeData.enableTime = true),
                        (timeData.noCalendar = true),
                        (timeData.dateFormat = "H:i");
                }
                if (isTimepickerVal["data-time-hrs"]) {
                    (timeData.enableTime = true),
                        (timeData.noCalendar = true),
                        (timeData.dateFormat = "H:i"),
                        (timeData.time_24hr = true);
                }
                if (isTimepickerVal["data-min-time"]) {
                    (timeData.enableTime = true),
                        (timeData.noCalendar = true),
                        (timeData.dateFormat = "H:i"),
                        (timeData.minTime = isTimepickerVal["data-min-time"].value.toString());
                }
                if (isTimepickerVal["data-max-time"]) {
                    (timeData.enableTime = true),
                        (timeData.noCalendar = true),
                        (timeData.dateFormat = "H:i"),
                        (timeData.minTime = isTimepickerVal["data-max-time"].value.toString());
                }
                if (isTimepickerVal["data-default-time"]) {
                    (timeData.enableTime = true),
                        (timeData.noCalendar = true),
                        (timeData.dateFormat = "H:i"),
                        (timeData.defaultDate = isTimepickerVal["data-default-time"].value.toString());
                }
                if (isTimepickerVal["data-time-inline"]) {
                    (timeData.enableTime = true),
                        (timeData.noCalendar = true),
                        (timeData.defaultDate = isTimepickerVal["data-time-inline"].value.toString());
                    timeData.inline = true;
                }
                flatpickr(item, timeData);
            }
        });

        // Dropdown
        Array.from(document.querySelectorAll('.dropdown-menu a[data-bs-toggle="tab"]')).forEach(function (element) {
            element.addEventListener("click", function (e) {
                e.stopPropagation();
                bootstrap.Tab.getInstance(e.target).show();
            });
        });
    }

    /**
     * Sidebar menu collapse
     */
    function isCollapseMenu() {
        if (document.querySelectorAll(".navbar-nav .collapse")) {
            const collapses = document.querySelectorAll(".navbar-nav .collapse");
            Array.from(collapses).forEach(function (collapse) {
                // Init collapses
                const collapseInstance = new bootstrap.Collapse(collapse, {
                    toggle: false,
                });
                // Hide sibling collapses on `show.bs.collapse`
                collapse.addEventListener("show.bs.collapse", function (e) {
                    e.stopPropagation();
                    const closestCollapse = collapse.parentElement.closest(".collapse");
                    if (closestCollapse) {
                        const siblingCollapses = closestCollapse.querySelectorAll(".collapse");
                        Array.from(siblingCollapses).forEach(function (siblingCollapse) {
                            var siblingCollapseInstance = bootstrap.Collapse.getInstance(siblingCollapse);
                            if (siblingCollapseInstance === collapseInstance) {
                                return;
                            }
                            siblingCollapseInstance.hide();
                        });
                    } else {
                        const getSiblings = function (elem) {
                            // Setup siblings array and get the first sibling
                            var siblings = [];
                            var sibling = elem.parentNode.firstChild;
                            // Loop through each sibling and push to the array
                            while (sibling) {
                                if (sibling.nodeType === 1 && sibling !== elem) {
                                    siblings.push(sibling);
                                }
                                sibling = sibling.nextSibling;
                            }
                            return siblings;
                        };
                        const siblings = getSiblings(collapse.parentElement);
                        Array.from(siblings).forEach(function (item) {
                            if (item.childNodes.length > 2)
                                item.firstElementChild.setAttribute("aria-expanded", "false");
                            const ids = item.querySelectorAll("*[id]");
                            Array.from(ids).forEach(function (item1) {
                                item1.classList.remove("show");
                                if (item1.childNodes.length > 2) {
                                    const val = item1.querySelectorAll("ul li a");
                                    Array.from(val).forEach(function (subitem) {
                                        if (subitem.hasAttribute("aria-expanded"))
                                            subitem.setAttribute("aria-expanded", "false");
                                    });
                                }
                            });
                        });
                    }
                });

                // Verschachtelte Ausklappungen ausblenden bei `hide.bs.collapse`
                collapse.addEventListener("hide.bs.collapse", function (e) {
                    e.stopPropagation();
                    const childCollapses = collapse.querySelectorAll(".collapse");
                    Array.from(childCollapses).forEach(function (childCollapse) {
                        childCollapseInstance = bootstrap.Collapse.getInstance(childCollapse);
                        childCollapseInstance.hide();
                    });
                });
            });
        }
    }

    /**
     * Zweispaltiges Menü erzeugen
     */
    function twoColumnMenuGenerate() {
        const isTwoColumn = document.documentElement.getAttribute("data-layout");
        const isValues = localStorage.getItem("defaultAttribute");
        const defaultValues = JSON.parse(isValues);

        if (defaultValues && (isTwoColumn === "twocolumn" || defaultValues["data-layout"] === "twocolumn")) {
            if (document.querySelector(".navbar-menu")) {
                document.querySelector(".navbar-menu").innerHTML = navbarMenuHTML;
            }
            const ul = document.createElement("ul");
            ul.innerHTML = '<a href="#" class="logo"><img src="/assets/images/logo-sm.png" alt="" height="22"></a>';
            Array.from(document.getElementById("navbar-nav").querySelectorAll(".menu-link")).forEach(function (item) {
                ul.className = "twocolumn-iconview";
                const li = document.createElement("li");
                const a = item;
                a.querySelectorAll("span").forEach(function (element) {
                    element.classList.add("d-none");
                });

                if (item.parentElement.classList.contains("twocolumn-item-show")) {
                    item.classList.add("active");
                }
                li.appendChild(a);
                ul.appendChild(li);

                a.classList.contains("nav-link") ? a.classList.replace("nav-link", "nav-icon") : "";
                a.classList.remove("collapsed", "menu-link");
            });
            let currentPath = location.pathname === "/" ? "/" : location.pathname.substring(1);
            currentPath = (currentPath === "/") ? "/" : currentPath.substring(currentPath.lastIndexOf("/") + 1);
            if (currentPath) {
                // navbar-nav
                const a = document.getElementById("navbar-nav").querySelector('[href="' + currentPath + '"]');

                if (a) {
                    const parentCollapseDiv = a.closest(".collapse.menu-dropdown");
                    if (parentCollapseDiv) {
                        parentCollapseDiv.classList.add("show");
                        parentCollapseDiv.parentElement.children[0].classList.add("active");
                        parentCollapseDiv.parentElement.children[0].setAttribute("aria-expanded", "true");
                        if (parentCollapseDiv.parentElement.closest(".collapse.menu-dropdown")) {
                            parentCollapseDiv.parentElement.closest(".collapse").classList.add("show");
                            if (parentCollapseDiv.parentElement.closest(".collapse").previousElementSibling)
                                parentCollapseDiv.parentElement.closest(".collapse").previousElementSibling.classList.add("active");
                            if (parentCollapseDiv.parentElement.parentElement.parentElement.parentElement.closest(".collapse.menu-dropdown")) {
                                parentCollapseDiv.parentElement.parentElement.parentElement.parentElement.closest(".collapse").classList.add("show");
                                if (parentCollapseDiv.parentElement.parentElement.parentElement.parentElement.closest(".collapse").previousElementSibling) {
                                    parentCollapseDiv.parentElement.parentElement.parentElement.parentElement.closest(".collapse").previousElementSibling.classList.add("active");
                                }
                            }
                        }
                    }
                }
            }
            // add all sidebar menu icons
            document.getElementById("two-column-menu").innerHTML = ul.outerHTML;

            // show submenu on sidebar menu click
            Array.from(document.querySelector("#two-column-menu ul").querySelectorAll("li a")).forEach(function (element) {
                let currentPath = location.pathname === "/" ? "/" : location.pathname.substring(1);
                currentPath = (currentPath === "/") ? "/" : currentPath.substring(currentPath.lastIndexOf("/") + 1);
                element.addEventListener("click", function (e) {
                    if (!(currentPath === "/" + element.getAttribute("href") && !element.getAttribute("data-bs-toggle")))
                        document.body.classList.contains("twocolumn-panel") ? document.body.classList.remove("twocolumn-panel") : "";
                    document.getElementById("navbar-nav").classList.remove("twocolumn-nav-hide");
                    document.querySelector(".hamburger-icon").classList.remove("open");
                    if ((e.target && e.target.matches("a.nav-icon")) || (e.target && e.target.matches("i"))) {
                        if (document.querySelector("#two-column-menu ul .nav-icon.active") !== null)
                            document.querySelector("#two-column-menu ul .nav-icon.active").classList.remove("active");
                        e.target.matches("i") ? e.target.closest("a").classList.add("active") : e.target.classList.add("active");

                        const twoColumnItem = document.getElementsByClassName("twocolumn-item-show");

                        twoColumnItem.length > 0 ? twoColumnItem[0].classList.remove("twocolumn-item-show") : "";

                        const currentMenu = e.target.matches("i") ? e.target.closest("a") : e.target;
                        const childMenusId = currentMenu.getAttribute("href").slice(1);
                        if (document.getElementById(childMenusId))
                            document.getElementById(childMenusId).parentElement.classList.add("twocolumn-item-show");
                    }
                });

                // add active class to the sidebar menu icon who has direct link
                if (currentPath === "/" + element.getAttribute("href") && !element.getAttribute("data-bs-toggle")) {
                    element.classList.add("active");
                    document.getElementById("navbar-nav").classList.add("twocolumn-nav-hide");
                    if (document.querySelector(".hamburger-icon")) {
                        document.querySelector(".hamburger-icon").classList.add("open");
                    }
                }
            });

            const currentLayout = document.documentElement.getAttribute("data-layout");
            if (currentLayout !== "horizontal") {
                const simpleBar = new SimpleBar(document.getElementById("navbar-nav"));
                if (simpleBar) simpleBar.getContentElement();

                const simpleBar1 = new SimpleBar(
                    document.getElementsByClassName("twocolumn-iconview")[0]
                );
                if (simpleBar1) simpleBar1.getContentElement();
            }
        }
    }

    //  Search menu dropdown on Topbar
    function isCustomDropdown() {
        //Search bar
        const searchOptions = document.getElementById("search-close-options");
        const dropdown = document.getElementById("search-dropdown");
        const searchInput = document.getElementById("search-options");
        if (searchInput) {
            searchInput.addEventListener("focus", function () {
                const inputLength = searchInput.value.length;
                if (inputLength > 0) {
                    dropdown.classList.add("show");
                    searchOptions.classList.remove("d-none");
                } else {
                    dropdown.classList.remove("show");
                    searchOptions.classList.add("d-none");
                }
            });

            searchInput.addEventListener("keyup", function (event) {
                const inputLength = searchInput.value.length;
                if (inputLength > 0) {
                    dropdown.classList.add("show");
                    searchOptions.classList.remove("d-none");

                    const inputVal = searchInput.value.toLowerCase();

                    const notifyItem = document.getElementsByClassName("notify-item");

                    Array.from(notifyItem).forEach(function (element) {
                        let notifiTxt = '';
                        if (element.querySelector("h6")) {
                            const spantext = element.getElementsByTagName("span")[0].innerText.toLowerCase();
                            const name = element.querySelector("h6").innerText.toLowerCase();
                            if (name.includes(inputVal)) {
                                notifiTxt = name
                            } else {
                                notifiTxt = spantext
                            }
                        } else if (element.getElementsByTagName("span")) {
                            notifiTxt = element.getElementsByTagName("span")[0].innerText.toLowerCase()
                        }

                        if (notifiTxt)
                            element.style.display = notifiTxt.includes(inputVal) ? "block" : "none";

                    });
                } else {
                    dropdown.classList.remove("show");
                    searchOptions.classList.add("d-none");
                }
            });

            searchOptions.addEventListener("click", function () {
                searchInput.value = "";
                dropdown.classList.remove("show");
                searchOptions.classList.add("d-none");
            });

            document.body.addEventListener("click", function (e) {
                if (e.target.getAttribute("id") !== "search-options") {
                    dropdown.classList.remove("show");
                    searchOptions.classList.add("d-none");
                }
            });
        }
    }
    //  search menu dropdown on topbar
    function isCustomDropdownResponsive() {
        //Search bar
        const searchOptions = document.getElementById("search-close-options");
        const dropdownReponsive = document.getElementById("search-dropdown-reponsive");
        const searchInputReponsive = document.getElementById("search-options-reponsive");

        if (searchOptions && dropdownReponsive && searchInputReponsive) {
            searchInputReponsive.addEventListener("focus", function () {
                const inputLength = searchInputReponsive.value.length;
                if (inputLength > 0) {
                    dropdownReponsive.classList.add("show");
                    searchOptions.classList.remove("d-none");
                } else {
                    dropdownReponsive.classList.remove("show");
                    searchOptions.classList.add("d-none");
                }
            });

            searchInputReponsive.addEventListener("keyup", function () {
                const inputLength = searchInputReponsive.value.length;
                if (inputLength > 0) {
                    dropdownReponsive.classList.add("show");
                    searchOptions.classList.remove("d-none");
                } else {
                    dropdownReponsive.classList.remove("show");
                    searchOptions.classList.add("d-none");
                }
            });

            searchOptions.addEventListener("click", function () {
                searchInputReponsive.value = "";
                dropdownReponsive.classList.remove("show");
                searchOptions.classList.add("d-none");
            });

            document.body.addEventListener("click", function (e) {
                if (e.target.getAttribute("id") !== "search-options") {
                    dropdownReponsive.classList.remove("show");
                    searchOptions.classList.add("d-none");
                }
            });
        }
    }

    function initLeftMenuCollapse() {
        /**
         * Vertical layout menu scroll add
         */
        if (document.documentElement.getAttribute("data-layout") === "vertical" || document.documentElement.getAttribute("data-layout") === "semibox") {
            const twoColumnMenu = document.getElementById("two-column-menu");
            if (twoColumnMenu) {
                twoColumnMenu.innerHTML = "";
            }
            if (document.querySelector(".navbar-menu")) {
                document.querySelector(".navbar-menu").innerHTML = navbarMenuHTML;
            }
            const scrollbar = document.getElementById("scrollbar");
            if (scrollbar) {
                scrollbar.setAttribute("data-simplebar", "");
                scrollbar.classList.add("h-100");
            }
        }

        /**
         * Two-column layout menu scroll add
         */
        if (document.documentElement.getAttribute("data-layout") === "twocolumn") {
            document.getElementById("scrollbar").removeAttribute("data-simplebar");
            document.getElementById("scrollbar").classList.remove("h-100");
        }

        /**
         * Horizontal layout menu
         */
        if (document.documentElement.getAttribute("data-layout") === "horizontal") {
            updateHorizontalMenus();
        }
    }

    function isLoadBodyElement() {
        const verticalOverlay = document.getElementsByClassName("vertical-overlay");
        if (verticalOverlay) {
            Array.from(verticalOverlay).forEach(function (element) {
                element.addEventListener("click", function () {
                    document.body.classList.remove("vertical-sidebar-enable");
                    if (localStorage.getItem("data-layout") === "twocolumn")
                        document.body.classList.add("twocolumn-panel");
                    else
                        document.documentElement.setAttribute("data-sidebar-size", localStorage.getItem("data-sidebar-size"));
                });
            });
        }
    }

    function windowResizeHover() {
        const windowSize = document.documentElement.clientWidth;
        if (windowSize < 1025 && windowSize > 767) {
            document.body.classList.remove("twocolumn-panel");
            if (localStorage.getItem("data-layout") === "twocolumn") {
                document.documentElement.setAttribute("data-layout", "twocolumn");
                if (document.getElementById("customizer-layout03")) {
                    document.getElementById("customizer-layout03").click();
                }
                twoColumnMenuGenerate();
                initTwoColumnActiveMenu();
                isCollapseMenu();
            }
            if (localStorage.getItem("data-layout") === "vertical") {
                document.documentElement.setAttribute("data-sidebar-size", "sm");
            }
            if (localStorage.getItem("data-layout") === "semibox") {
                document.documentElement.setAttribute("data-sidebar-size", "sm");
            }
            if (document.querySelector(".hamburger-icon")) {
                document.querySelector(".hamburger-icon").classList.add("open");
            }
        } else if (windowSize >= 1025) {
            document.body.classList.remove("twocolumn-panel");
            if (localStorage.getItem("data-layout") === "twocolumn") {
                document.documentElement.setAttribute("data-layout", "twocolumn");
                if (document.getElementById("customizer-layout03")) {
                    document.getElementById("customizer-layout03").click();
                }
                twoColumnMenuGenerate();
                initTwoColumnActiveMenu();
                isCollapseMenu();
            }
            if (localStorage.getItem("data-layout") === "vertical") {
                document.documentElement.setAttribute(
                    "data-sidebar-size",
                    localStorage.getItem("data-sidebar-size")
                );
            }
            if (localStorage.getItem("data-layout") === "semibox") {
                document.documentElement.setAttribute("data-sidebar-size", localStorage.getItem("data-sidebar-size"));
            }
            if (document.querySelector(".hamburger-icon")) {
                document.querySelector(".hamburger-icon").classList.remove("open");
            }
        } else if (windowSize <= 767) {
            document.body.classList.remove("vertical-sidebar-enable");
            document.body.classList.add("twocolumn-panel");
            if (localStorage.getItem("data-layout") === "twocolumn") {
                document.documentElement.setAttribute("data-layout", "vertical");
                hideShowLayoutOptions("vertical");
                isCollapseMenu();
            }
            if (localStorage.getItem("data-layout") !== "horizontal") {
                document.documentElement.setAttribute("data-sidebar-size", "lg");
            }
            if (document.querySelector(".hamburger-icon")) {
                document.querySelector(".hamburger-icon").classList.add("open");
            }
        }

        const isElement = document.querySelectorAll("#navbar-nav > li.nav-item");
        Array.from(isElement).forEach(function (item) {
            item.addEventListener("click", menuItem.bind(this), false);
            item.addEventListener("mouseover", menuItem.bind(this), false);
        });
    }

    function menuItem(e) {
        let elements;
        let eleChild;
        if (e.target && e.target.matches("a.nav-link span")) {
            if (elementInViewport(e.target.parentElement.nextElementSibling) === false) {
                e.target.parentElement.nextElementSibling.classList.add("dropdown-custom-right");
                e.target.parentElement.parentElement.parentElement.parentElement.classList.add("dropdown-custom-right");
                eleChild = e.target.parentElement.nextElementSibling;
                Array.from(eleChild.querySelectorAll(".menu-dropdown")).forEach(function (item) {
                    item.classList.add("dropdown-custom-right");
                });
            } else if (elementInViewport(e.target.parentElement.nextElementSibling) === true) {
                if (window.innerWidth >= 1848) {
                    elements = document.getElementsByClassName("dropdown-custom-right");
                    while (elements.length > 0) {
                        elements[0].classList.remove("dropdown-custom-right");
                    }
                }
            }
        }

        if (e.target && e.target.matches("a.nav-link")) {
            if (elementInViewport(e.target.nextElementSibling) === false) {
                e.target.nextElementSibling.classList.add("dropdown-custom-right");
                e.target.parentElement.parentElement.parentElement.classList.add("dropdown-custom-right");
                eleChild = e.target.nextElementSibling;
                Array.from(eleChild.querySelectorAll(".menu-dropdown")).forEach(function (item) {
                    item.classList.add("dropdown-custom-right");
                });
            } else if (elementInViewport(e.target.nextElementSibling) === true) {
                if (window.innerWidth >= 1848) {
                    elements = document.getElementsByClassName("dropdown-custom-right");
                    while (elements.length > 0) {
                        elements[0].classList.remove("dropdown-custom-right");
                    }
                }
            }
        }
    }

    function toggleHamburgerMenu() {
        const windowSize = document.documentElement.clientWidth;

        if (windowSize > 767)
            document.querySelector(".hamburger-icon").classList.toggle("open");

        //For collapse horizontal menu
        if (document.documentElement.getAttribute("data-layout") === "horizontal") {
            document.body.classList.contains("menu") ? document.body.classList.remove("menu") : document.body.classList.add("menu");
        }

        //For collapse vertical menu
        if (document.documentElement.getAttribute("data-layout") === "vertical") {
            if (windowSize <= 1025 && windowSize > 767) {
                document.body.classList.remove("vertical-sidebar-enable");
                document.documentElement.getAttribute("data-sidebar-size") === "sm" ?
                    document.documentElement.setAttribute("data-sidebar-size", "") :
                    document.documentElement.setAttribute("data-sidebar-size", "sm");
            } else if (windowSize > 1025) {
                document.body.classList.remove("vertical-sidebar-enable");
                document.documentElement.getAttribute("data-sidebar-size") === "lg" ?
                    document.documentElement.setAttribute("data-sidebar-size", "sm") :
                    document.documentElement.setAttribute("data-sidebar-size", "lg");
            } else if (windowSize <= 767) {
                document.body.classList.add("vertical-sidebar-enable");
                document.documentElement.setAttribute("data-sidebar-size", "lg");
            }
        }

        // semibox menu
        if (document.documentElement.getAttribute("data-layout") === "semibox") {
            if (windowSize > 767) {
                // (document.querySelector(".hamburger-icon").classList.contains("open")) ? document.documentElement.setAttribute('data-sidebar-visibility', "show"): '';
                if(document.documentElement.getAttribute('data-sidebar-visibility') === "show") {
                    document.documentElement.getAttribute("data-sidebar-size") === "lg" ?
                        document.documentElement.setAttribute("data-sidebar-size", "sm") :
                        document.documentElement.setAttribute("data-sidebar-size", "lg");
                } else {
                    document.getElementById("sidebar-visibility-show").click();
                    document.documentElement.setAttribute("data-sidebar-size", document.documentElement.getAttribute("data-sidebar-size"));
                }
            } else if (windowSize <= 767) {
                document.body.classList.add("vertical-sidebar-enable");
                document.documentElement.setAttribute("data-sidebar-size", "lg");
            }
        }

        //Two column menu
        if (document.documentElement.getAttribute("data-layout") === "twocolumn") {
            document.body.classList.contains("twocolumn-panel") ?
                document.body.classList.remove("twocolumn-panel") :
                document.body.classList.add("twocolumn-panel");
        }
    }

    function windowLoadContent() {
        // Demo show code
        document.addEventListener("DOMContentLoaded", function () {
            const checkbox = document.getElementsByClassName("code-switcher");
            Array.from(checkbox).forEach(function (check) {
                check.addEventListener("change", function () {
                    const card = check.closest(".card");
                    const preview = card.querySelector(".live-preview");
                    const code = card.querySelector(".code-view");

                    if (check.checked) {
                        preview.classList.add("d-none");
                        code.classList.remove("d-none");
                    } else {
                        preview.classList.remove("d-none");
                        code.classList.add("d-none");
                    }
                });
            });
        });

        window.addEventListener("resize", windowResizeHover);
        windowResizeHover();

        document.addEventListener("scroll", function () {
            windowScroll();
        });

        window.addEventListener("load", function () {
            const isTwoColumn = document.documentElement.getAttribute("data-layout");
            if (isTwoColumn === "twocolumn") {
                initTwoColumnActiveMenu();
            } else {
                initActiveMenu();
            }
            isLoadBodyElement();
            addEventListenerOnSmHoverMenu();
        });
        if (document.getElementById("topnav-hamburger-icon")) {
            document.getElementById("topnav-hamburger-icon").addEventListener("click", toggleHamburgerMenu);
        }
        const isValues = localStorage.getItem("defaultAttribute");
        const defaultValues = JSON.parse(isValues);
        const windowSize = document.documentElement.clientWidth;

        if (defaultValues["data-layout"] === "twocolumn" && windowSize < 767) {
            Array.from(document.getElementById("two-column-menu").querySelectorAll("li")).forEach(function (item) {
                item.addEventListener("click", function (e) {
                    document.body.classList.remove("twocolumn-panel");
                });
            });
        }
    }

    // page topbar class added
    function windowScroll() {
        const pageTopbar = document.getElementById("page-topbar");
        if (pageTopbar) {
            document.body.scrollTop >= 50 || document.documentElement.scrollTop >= 50 ? pageTopbar.classList.add("topbar-shadow") : pageTopbar.classList.remove("topbar-shadow");
        }
    }

    // Two-column menu activation
    function initTwoColumnActiveMenu() {
        let menuId;
        // two column sidebar active js
        let currentPath = location.pathname === "/" ? "/" : location.pathname.substring(1);
        currentPath = (currentPath === "/") ? "/" : currentPath.substring(currentPath.lastIndexOf("/") + 1);
        if (currentPath) {
            if (document.body.className === "twocolumn-panel") {
                document.getElementById("two-column-menu").querySelector('[href="' + currentPath + '"]').classList.add("active");
            }
            // navbar-nav
            const navbarNav = document.getElementById("navbar-nav");
            if (!navbarNav) {
                console.warn("Element #navbar-nav not found! Please check the HTML markup.");
                return;
            }
            const a = navbarNav.querySelector('[href="' + currentPath + '"]');
            if (a) {
                a.classList.add("active");
                const parentCollapseDiv = a.closest(".collapse.menu-dropdown");
                if (parentCollapseDiv && parentCollapseDiv.parentElement.closest(".collapse.menu-dropdown")) {
                    parentCollapseDiv.classList.add("show");
                    parentCollapseDiv.parentElement.children[0].classList.add("active");
                    parentCollapseDiv.parentElement.closest(".collapse.menu-dropdown").parentElement.classList.add("twocolumn-item-show");
                    if (parentCollapseDiv.parentElement.parentElement.parentElement.parentElement.closest(".collapse.menu-dropdown")) {
                        const menuIdSub = parentCollapseDiv.parentElement.parentElement.parentElement.parentElement.closest(".collapse.menu-dropdown").getAttribute("id");
                        parentCollapseDiv.parentElement.parentElement.parentElement.parentElement.closest(".collapse.menu-dropdown").parentElement.classList.add("twocolumn-item-show");
                        parentCollapseDiv.parentElement.closest(".collapse.menu-dropdown").parentElement.classList.remove("twocolumn-item-show");
                        if (document.getElementById("two-column-menu").querySelector('[href="#' + menuIdSub + '"]'))
                            document.getElementById("two-column-menu").querySelector('[href="#' + menuIdSub + '"]').classList.add("active");
                    }
                    menuId = parentCollapseDiv.parentElement.closest(".collapse.menu-dropdown").getAttribute("id");
                    if (document.getElementById("two-column-menu").querySelector('[href="#' + menuId + '"]'))
                        document.getElementById("two-column-menu").querySelector('[href="#' + menuId + '"]').classList.add("active");
                } else {
                    a.closest(".collapse.menu-dropdown").parentElement.classList.add("twocolumn-item-show");
                    menuId = parentCollapseDiv.getAttribute("id");
                    if (document.getElementById("two-column-menu").querySelector('[href="#' + menuId + '"]'))
                        document.getElementById("two-column-menu").querySelector('[href="#' + menuId + '"]').classList.add("active");
                }
            } else {
                document.body.classList.add("twocolumn-panel");
            }
        }
    }

    // two-column sidebar active js
    function initActiveMenu() {
        const navbarNav = document.getElementById("navbar-nav");

        if (!navbarNav) {
            return;
        }

        let currentPath = location.pathname === "/" ? "/" : location.pathname.substring(1);
        currentPath = (currentPath === "/") ? "/" : currentPath.substring(currentPath.lastIndexOf("/") + 1);

        const a = navbarNav.querySelector('[href="' + currentPath + '"]');
        if (a) {
            a.classList.add("active");
            const parentCollapseDiv = a.closest(".collapse.menu-dropdown");
            if (parentCollapseDiv) {
                parentCollapseDiv.classList.add("show");
                parentCollapseDiv.parentElement.children[0].classList.add("active");
                parentCollapseDiv.parentElement.children[0].setAttribute("aria-expanded", "true");
            }
        }
    }

    function elementInViewport(el) {
        if (el) {
            let top = el.offsetTop;
            let left = el.offsetLeft;
            const width = el.offsetWidth;
            const height = el.offsetHeight;

            if (el.offsetParent) {
                while (el.offsetParent) {
                    el = el.offsetParent;
                    top += el.offsetTop;
                    left += el.offsetLeft;
                }
            }
            return (
                top >= window.pageYOffset &&
                left >= window.pageXOffset &&
                top + height <= window.pageYOffset + window.innerHeight &&
                left + width <= window.pageXOffset + window.innerWidth
            );
        }
    }

    // notification cart dropdown
    function initTopbarComponents() {
        if (document.getElementsByClassName("dropdown-item-cart")) {
            let dropdownItemCart = document.querySelectorAll(".dropdown-item-cart").length;
            Array.from(document.querySelectorAll("#page-topbar .dropdown-menu-cart .remove-item-btn")).forEach(function (item) {
                item.addEventListener("click", function (e) {
                    dropdownItemCart--;
                    this.closest(".dropdown-item-cart").remove();
                    Array.from(document.getElementsByClassName("cartitem-badge")).forEach(function (e) {
                        e.innerHTML = dropdownItemCart;
                    });
                    updateCartPrice();
                    if (document.getElementById("empty-cart")) {
                        document.getElementById("empty-cart").style.display = dropdownItemCart === 0 ? "block" : "none";
                    }
                    if (document.getElementById("checkout-elem")) {
                        document.getElementById("checkout-elem").style.display = dropdownItemCart === 0 ? "none" : "block";
                    }
                });
            });
            Array.from(document.getElementsByClassName("cartitem-badge")).forEach(function (e) {
                e.innerHTML = dropdownItemCart;
            });
            if (document.getElementById("empty-cart")) {
                document.getElementById("empty-cart").style.display = "none";
            }
            if (document.getElementById("checkout-elem")) {
                document.getElementById("checkout-elem").style.display = "block";
            }
            function updateCartPrice() {
                const currencySign = "$";
                let subtotal = 0;
                Array.from(document.getElementsByClassName("cart-item-price")).forEach(function (e) {
                    subtotal += parseFloat(e.innerHTML);
                });
                if (document.getElementById("cart-item-total")) {
                    document.getElementById("cart-item-total").innerHTML = currencySign + subtotal.toFixed(2);
                }
            }
            updateCartPrice();
        }

        // notification messages
        if (document.getElementsByClassName("notification-check")) {
            function emptyNotification() {
                Array.from(document.querySelectorAll("#notificationItemsTabContent .tab-pane")).forEach(function (elem) {
                    if (elem.querySelectorAll(".notification-item").length > 0) {
                        if (elem.querySelector(".view-all")) {
                            elem.querySelector(".view-all").style.display = "block";
                        }
                    } else {
                        if (elem.querySelector(".view-all")) {
                            elem.querySelector(".view-all").style.display = "none";
                        }
                        const emptyNotificationElem = elem.querySelector(".empty-notification-elem");
                        if (!emptyNotificationElem) {
                            elem.innerHTML += '<div class="empty-notification-elem">\
							<div class="w-25 w-sm-50 pt-3 mx-auto">\
								<img src="/assets/images/svg/bell.svg" class="img-fluid" alt="user-pic">\
							</div>\
							<div class="text-center pb-5 mt-2">\
								<h6 class="fs-18 fw-semibold lh-base">Hey! You have no any notifications </h6>\
							</div>\
						</div>'
                        }
                    }
                });
            }
            emptyNotification();


            Array.from(document.querySelectorAll(".notification-check input")).forEach(function (element) {
                element.addEventListener("change", function (el) {
                    el.target.closest(".notification-item").classList.toggle("active");

                    const checkedCount = document.querySelectorAll('.notification-check input:checked').length;

                    if (el.target.closest(".notification-item").classList.contains("active")) {
                        (checkedCount > 0) ? document.getElementById("notification-actions").style.display = 'block' : document.getElementById("notification-actions").style.display = 'none';
                    } else {
                        (checkedCount > 0) ? document.getElementById("notification-actions").style.display = 'block' : document.getElementById("notification-actions").style.display = 'none';
                    }
                    document.getElementById("select-content").innerHTML = checkedCount
                });

                const notificationDropdown = document.getElementById('notificationDropdown');
                notificationDropdown.addEventListener('hide.bs.dropdown', function (event) {
                    element.checked = false;
                    document.querySelectorAll('.notification-item').forEach(function (item) {
                        item.classList.remove("active");
                    })
                    document.getElementById('notification-actions').style.display = '';
                });
            });
        }
    }

    function initComponents() {
        // tooltip
        const tooltipTriggerList = [].slice.call(
            document.querySelectorAll('[data-bs-toggle="tooltip"]')
        );
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // popover
        const popoverTriggerList = [].slice.call(
            document.querySelectorAll('[data-bs-toggle="popover"]')
        );
        popoverTriggerList.map(function (popoverTriggerEl) {
            return new bootstrap.Popover(popoverTriggerEl);
        });
    }

    // Counter Number
    function counter() {
        const counter = document.querySelectorAll(".counter-value");
        const speed = 250; // The lower, the slower
        counter &&
        Array.from(counter).forEach(function (counter_value) {
            function updateCount() {
                const target = +counter_value.getAttribute("data-target");
                const count = +counter_value.innerText;
                let inc = target / speed;
                if (inc < 1) {
                    inc = 1;
                }
                // Check if target is reached
                if (count < target) {
                    // Add inc to count and output in counter_value
                    counter_value.innerText = (count + inc).toFixed(0);
                    // Call function every ms
                    setTimeout(updateCount, 1);
                } else {
                    counter_value.innerText = numberWithCommas(target);
                }
                numberWithCommas(counter_value.innerText);
            }
            updateCount();
        });

        function numberWithCommas(x) {
            return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }
    }

    function updateHorizontalMenus() {
        document.getElementById("two-column-menu").innerHTML = "";
        if (document.querySelector(".navbar-menu")) {
            document.querySelector(".navbar-menu").innerHTML = navbarMenuHTML;
        }
        document.getElementById("scrollbar").removeAttribute("data-simplebar");
        document.getElementById("navbar-nav").removeAttribute("data-simplebar");
        document.getElementById("scrollbar").classList.remove("h-100");

        const splitMenu = horizontalMenuSplit;
        const extraMenuName = "More";
        const menuData = document.querySelectorAll("ul.navbar-nav > li.nav-item");
        let newMenus = "";
        let splitItem = "";

        Array.from(menuData).forEach(function (item, index) {
            if (index + 1 === splitMenu) {
                splitItem = item;
            }
            if (index + 1 > splitMenu) {
                newMenus += item.outerHTML;
                item.remove();
            }

            if (index + 1 === menuData.length) {
                if (splitItem.insertAdjacentHTML) {
                    splitItem.insertAdjacentHTML(
                        "afterend",
                        '<li class="nav-item">\
                        <a class="nav-link" href="#sidebarMore" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarMore">\
                            <i class="ri-briefcase-2-line"></i> ' + extraMenuName + '\
						</a>\
						<div class="collapse menu-dropdown" id="sidebarMore"><ul class="nav nav-sm flex-column">' + newMenus + "</ul></div>\
					</li>");
                }
            }
        });
    }

    function hideShowLayoutOptions(dataLayout) {
        if (dataLayout === "vertical") {
            const twoColumnMenu = document.getElementById("two-column-menu");
            if (twoColumnMenu) {
                twoColumnMenu.innerHTML = "";
            }
            if (document.querySelector(".navbar-menu")) {
                document.querySelector(".navbar-menu").innerHTML = navbarMenuHTML;
            }
            if (document.getElementById("theme-settings-offcanvas")) {
                document.getElementById("sidebar-size").style.display = "block";
                document.getElementById("sidebar-view").style.display = "block";
                document.getElementById("sidebar-color").style.display = "block";
                if (document.getElementById("sidebar-img")) {
                    document.getElementById("sidebar-img").style.display = "block";
                }
                document.getElementById("layout-position").style.display = "block";
                document.getElementById("layout-width").style.display = "block";
                document.getElementById("sidebar-visibility").style.display = "none";
            }
            initLeftMenuCollapse();
            initActiveMenu();
            addEventListenerOnSmHoverMenu();
            initMenuItemScroll();
        } else if (dataLayout === "horizontal") {
            updateHorizontalMenus();
            if (document.getElementById("theme-settings-offcanvas")) {
                document.getElementById("sidebar-size").style.display = "none";
                document.getElementById("sidebar-view").style.display = "none";
                document.getElementById("sidebar-color").style.display = "none";
                if (document.getElementById("sidebar-img")) {
                    document.getElementById("sidebar-img").style.display = "none";
                }
                document.getElementById("layout-position").style.display = "block";
                document.getElementById("layout-width").style.display = "block";
                document.getElementById("sidebar-visibility").style.display = "none";
            }
            initActiveMenu();
        } else if (dataLayout === "twocolumn") {
            document.getElementById("scrollbar").removeAttribute("data-simplebar");
            document.getElementById("scrollbar").classList.remove("h-100");
            if (document.getElementById("theme-settings-offcanvas")) {
                document.getElementById("sidebar-size").style.display = "none";
                document.getElementById("sidebar-view").style.display = "none";
                document.getElementById("sidebar-color").style.display = "block";
                if (document.getElementById("sidebar-img")) {
                    document.getElementById("sidebar-img").style.display = "block";
                }
                document.getElementById("layout-position").style.display = "none";
                document.getElementById("layout-width").style.display = "none";
                document.getElementById("sidebar-visibility").style.display = "none";
            }
        } else if (dataLayout === "semibox") {
            document.getElementById("two-column-menu").innerHTML = "";
            if (document.querySelector(".navbar-menu")) {
                document.querySelector(".navbar-menu").innerHTML = navbarMenuHTML;
            }
            if (document.getElementById("theme-settings-offcanvas")) {
                document.getElementById("sidebar-size").style.display = "block";
                document.getElementById("sidebar-view").style.display = "none";
                document.getElementById("sidebar-color").style.display = "block";
                if (document.getElementById("sidebar-img")) {
                    document.getElementById("sidebar-img").style.display = "block";
                }
                document.getElementById("layout-position").style.display = "block";
                document.getElementById("layout-width").style.display = "none";
                document.getElementById("sidebar-visibility").style.display = "block";
            }
            initLeftMenuCollapse();
            initActiveMenu();
            addEventListenerOnSmHoverMenu();
            initMenuItemScroll();
        }
    }

    // add listener Sidebar Hover icon on change layout from setting
    function addEventListenerOnSmHoverMenu() {
        const verticalHover = document.getElementById("vertical-hover");

        if (!verticalHover) {
            return;
        }

        verticalHover.addEventListener("click", function () {
            if (document.documentElement.getAttribute("data-sidebar-size") === "sm-hover") {
                document.documentElement.setAttribute("data-sidebar-size", "sm-hover-active");
            } else if (document.documentElement.getAttribute("data-sidebar-size") === "sm-hover-active") {
                document.documentElement.setAttribute("data-sidebar-size", "sm-hover");
            } else {
                document.documentElement.setAttribute("data-sidebar-size", "sm-hover");
            }
        });
    }
    // set full layout
    function layoutSwitch(isLayoutAttributes) {
        switch (isLayoutAttributes) {
            case isLayoutAttributes:
                switch (isLayoutAttributes["data-layout"]) {
                    case "vertical":
                        getElementUsingTagname("data-layout", "vertical");
                        localStorage.setItem("data-layout", "vertical");
                        document.documentElement.setAttribute("data-layout", "vertical");
                        hideShowLayoutOptions("vertical");
                        isCollapseMenu();
                        break;
                    case "horizontal":
                        getElementUsingTagname("data-layout", "horizontal");
                        localStorage.setItem("data-layout", "horizontal");
                        document.documentElement.setAttribute("data-layout", "horizontal");
                        hideShowLayoutOptions("horizontal");
                        break;
                    case "twocolumn":
                        getElementUsingTagname("data-layout", "twocolumn");
                        localStorage.setItem("data-layout", "twocolumn");
                        document.documentElement.setAttribute("data-layout", "twocolumn");
                        hideShowLayoutOptions("twocolumn");
                        break;
                    case "semibox":
                        getElementUsingTagname("data-layout", "semibox");
                        localStorage.setItem("data-layout", "semibox");
                        document.documentElement.setAttribute("data-layout", "semibox");
                        hideShowLayoutOptions("semibox");
                        break;
                    default:
                        if (localStorage.getItem("data-layout") === "vertical" && localStorage.getItem("data-layout")) {
                            getElementUsingTagname("data-layout", "vertical");
                            localStorage.setItem("data-layout", "vertical");
                            document.documentElement.setAttribute("data-layout", "vertical");
                            hideShowLayoutOptions("vertical");
                            isCollapseMenu();
                        } else if (localStorage.getItem("data-layout") === "horizontal") {
                            getElementUsingTagname("data-layout", "horizontal");
                            localStorage.setItem("data-layout", "horizontal");
                            document.documentElement.setAttribute("data-layout", "horizontal");
                            hideShowLayoutOptions("horizontal");
                        } else if (localStorage.getItem("data-layout") === "twocolumn") {
                            getElementUsingTagname("data-layout", "twocolumn");
                            localStorage.setItem("data-layout", "twocolumn");
                            document.documentElement.setAttribute("data-layout", "twocolumn");
                            hideShowLayoutOptions("twocolumn");
                        } else if (localStorage.getItem("data-layout") === "semibox") {
                            getElementUsingTagname("data-layout", "semibox");
                            localStorage.setItem("data-layout", "semibox");
                            document.documentElement.setAttribute("data-layout", "semibox");
                            hideShowLayoutOptions("semibox");
                        }
                        break;
                }
                switch (isLayoutAttributes["data-topbar"]) {
                    case "light":
                        getElementUsingTagname("data-topbar", "light");
                        localStorage.setItem("data-topbar", "light");
                        document.documentElement.setAttribute("data-topbar", "light");
                        break;
                    case "dark":
                        getElementUsingTagname("data-topbar", "dark");
                        localStorage.setItem("data-topbar", "dark");
                        document.documentElement.setAttribute("data-topbar", "dark");
                        break;
                    default:
                        if (localStorage.getItem("data-topbar") === "dark") {
                            getElementUsingTagname("data-topbar", "dark");
                            localStorage.setItem("data-topbar", "dark");
                            document.documentElement.setAttribute("data-topbar", "dark");
                        } else {
                            getElementUsingTagname("data-topbar", "light");
                            localStorage.setItem("data-topbar", "light");
                            document.documentElement.setAttribute("data-topbar", "light");
                        }
                        break;
                }

                switch (isLayoutAttributes["data-sidebar-visibility"]) {
                    case "hidden":
                        getElementUsingTagname("data-sidebar-visibility", "hidden");
                        localStorage.setItem("data-sidebar-visibility", "hidden");
                        document.documentElement.setAttribute("data-sidebar-visibility", "hidden");
                        break;
                    default:
                        getElementUsingTagname("data-sidebar-visibility", "show");
                        localStorage.setItem("data-sidebar-visibility", "show");
                        document.documentElement.setAttribute("data-sidebar-visibility", "show");
                        break;
                }

                switch (isLayoutAttributes["data-layout-style"]) {
                    case "default":
                        getElementUsingTagname("data-layout-style", "default");
                        localStorage.setItem("data-layout-style", "default");
                        document.documentElement.setAttribute("data-layout-style", "default");
                        break;
                    case "detached":
                        getElementUsingTagname("data-layout-style", "detached");
                        localStorage.setItem("data-layout-style", "detached");
                        document.documentElement.setAttribute("data-layout-style", "detached");
                        break;
                    default:
                        if (localStorage.getItem("data-layout-style") === "detached") {
                            getElementUsingTagname("data-layout-style", "detached");
                            localStorage.setItem("data-layout-style", "detached");
                            document.documentElement.setAttribute("data-layout-style", "detached");
                        } else {
                            getElementUsingTagname("data-layout-style", "default");
                            localStorage.setItem("data-layout-style", "default");
                            document.documentElement.setAttribute("data-layout-style", "default");
                        }
                        break;
                }

                switch (isLayoutAttributes["data-sidebar-size"]) {
                    case "lg":
                        getElementUsingTagname("data-sidebar-size", "lg");
                        document.documentElement.setAttribute("data-sidebar-size", "lg");
                        localStorage.setItem("data-sidebar-size", "lg");
                        break;

                    case "sm":
                        getElementUsingTagname("data-sidebar-size", "sm");
                        document.documentElement.setAttribute("data-sidebar-size", "sm");
                        localStorage.setItem("data-sidebar-size", "sm");
                        break;

                    case "md":
                        getElementUsingTagname("data-sidebar-size", "md");
                        document.documentElement.setAttribute("data-sidebar-size", "md");
                        localStorage.setItem("data-sidebar-size", "md");
                        break;

                    case "sm-hover":
                        getElementUsingTagname("data-sidebar-size", "sm-hover");
                        document.documentElement.setAttribute("data-sidebar-size", "sm-hover");
                        localStorage.setItem("data-sidebar-size", "sm-hover");
                        break;

                    default:
                        if (localStorage.getItem("data-sidebar-size") === "sm") {
                            document.documentElement.setAttribute("data-sidebar-size", "sm");
                            getElementUsingTagname("data-sidebar-size", "sm");
                            localStorage.setItem("data-sidebar-size", "sm");
                        } else if (localStorage.getItem("data-sidebar-size") === "md") {
                            document.documentElement.setAttribute("data-sidebar-size", "md");
                            getElementUsingTagname("data-sidebar-size", "md");
                            localStorage.setItem("data-sidebar-size", "md");
                        } else if (localStorage.getItem("data-sidebar-size") === "sm-hover") {
                            document.documentElement.setAttribute("data-sidebar-size", "sm-hover");
                            getElementUsingTagname("data-sidebar-size", "sm-hover");
                            localStorage.setItem("data-sidebar-size", "sm-hover");
                        } else {
                            document.documentElement.setAttribute("data-sidebar-size", "lg");
                            getElementUsingTagname("data-sidebar-size", "lg");
                            localStorage.setItem("data-sidebar-size", "lg");
                        }
                        break;
                }

                switch (isLayoutAttributes["data-bs-theme"]) {
                    case "light":
                        getElementUsingTagname("data-bs-theme", "light");
                        document.documentElement.setAttribute("data-bs-theme", "light");
                        localStorage.setItem("data-bs-theme", "light");
                        break;
                    case "dark":
                        getElementUsingTagname("data-bs-theme", "dark");
                        document.documentElement.setAttribute("data-bs-theme", "dark");
                        localStorage.setItem("data-bs-theme", "dark");
                        break;
                    default:
                        if (localStorage.getItem("data-bs-theme") && localStorage.getItem("data-bs-theme") === "dark") {
                            localStorage.setItem("data-bs-theme", "dark");
                            document.documentElement.setAttribute("data-bs-theme", "dark");
                            getElementUsingTagname("data-bs-theme", "dark");
                        } else {
                            localStorage.setItem("data-bs-theme", "light");
                            document.documentElement.setAttribute("data-bs-theme", "light");
                            getElementUsingTagname("data-bs-theme", "light");
                        }
                        break;
                }

                switch (isLayoutAttributes["data-layout-width"]) {
                    case "fluid":
                        getElementUsingTagname("data-layout-width", "fluid");
                        document.documentElement.setAttribute("data-layout-width", "fluid");
                        localStorage.setItem("data-layout-width", "fluid");
                        break;
                    case "boxed":
                        getElementUsingTagname("data-layout-width", "boxed");
                        document.documentElement.setAttribute("data-layout-width", "boxed");
                        localStorage.setItem("data-layout-width", "boxed");
                        break;
                    default:
                        if (localStorage.getItem("data-layout-width") === "boxed") {
                            localStorage.setItem("data-layout-width", "boxed");
                            document.documentElement.setAttribute("data-layout-width", "boxed");
                            getElementUsingTagname("data-layout-width", "boxed");
                        } else {
                            localStorage.setItem("data-layout-width", "fluid");
                            document.documentElement.setAttribute("data-layout-width", "fluid");
                            getElementUsingTagname("data-layout-width", "fluid");
                        }
                        break;
                }

                switch (isLayoutAttributes["data-sidebar"]) {
                    case "light":
                        getElementUsingTagname("data-sidebar", "light");
                        localStorage.setItem("data-sidebar", "light");
                        document.documentElement.setAttribute("data-sidebar", "light");
                        break;
                    case "dark":
                        getElementUsingTagname("data-sidebar", "dark");
                        localStorage.setItem("data-sidebar", "dark");
                        document.documentElement.setAttribute("data-sidebar", "dark");
                        break;
                    case "gradient":
                        getElementUsingTagname("data-sidebar", "gradient");
                        localStorage.setItem("data-sidebar", "gradient");
                        document.documentElement.setAttribute("data-sidebar", "gradient");
                        break;
                    case "gradient-2":
                        getElementUsingTagname("data-sidebar", "gradient-2");
                        localStorage.setItem("data-sidebar", "gradient-2");
                        document.documentElement.setAttribute("data-sidebar", "gradient-2");
                        break;
                    case "gradient-3":
                        getElementUsingTagname("data-sidebar", "gradient-3");
                        localStorage.setItem("data-sidebar", "gradient-3");
                        document.documentElement.setAttribute("data-sidebar", "gradient-3");
                        break;
                    case "gradient-4":
                        getElementUsingTagname("data-sidebar", "gradient-4");
                        localStorage.setItem("data-sidebar", "gradient-4");
                        document.documentElement.setAttribute("data-sidebar", "gradient-4");
                        break;
                    default:
                        if (localStorage.getItem("data-sidebar") && localStorage.getItem("data-sidebar") === "light") {
                            localStorage.setItem("data-sidebar", "light");
                            getElementUsingTagname("data-sidebar", "light");
                            document.documentElement.setAttribute("data-sidebar", "light");
                        } else if (localStorage.getItem("data-sidebar") === "dark") {
                            localStorage.setItem("data-sidebar", "dark");
                            getElementUsingTagname("data-sidebar", "dark");
                            document.documentElement.setAttribute("data-sidebar", "dark");
                        } else if (localStorage.getItem("data-sidebar") === "gradient") {
                            localStorage.setItem("data-sidebar", "gradient");
                            getElementUsingTagname("data-sidebar", "gradient");
                            document.documentElement.setAttribute("data-sidebar", "gradient");
                        } else if (localStorage.getItem("data-sidebar") === "gradient-2") {
                            localStorage.setItem("data-sidebar", "gradient-2");
                            getElementUsingTagname("data-sidebar", "gradient-2");
                            document.documentElement.setAttribute("data-sidebar", "gradient-2");
                        } else if (localStorage.getItem("data-sidebar") === "gradient-3") {
                            localStorage.setItem("data-sidebar", "gradient-3");
                            getElementUsingTagname("data-sidebar", "gradient-3");
                            document.documentElement.setAttribute("data-sidebar", "gradient-3");
                        } else if (localStorage.getItem("data-sidebar") === "gradient-4") {
                            localStorage.setItem("data-sidebar", "gradient-4");
                            getElementUsingTagname("data-sidebar", "gradient-4");
                            document.documentElement.setAttribute("data-sidebar", "gradient-4");
                        }
                        break;
                }

                switch (isLayoutAttributes["data-sidebar-image"]) {
                    case "none":
                        getElementUsingTagname("data-sidebar-image", "none");
                        localStorage.setItem("data-sidebar-image", "none");
                        document.documentElement.setAttribute("data-sidebar-image", "none");
                        break;
                    case "img-1":
                        getElementUsingTagname("data-sidebar-image", "img-1");
                        localStorage.setItem("data-sidebar-image", "img-1");
                        document.documentElement.setAttribute("data-sidebar-image", "img-1");
                        break;
                    case "img-2":
                        getElementUsingTagname("data-sidebar-image", "img-2");
                        localStorage.setItem("data-sidebar-image", "img-2");
                        document.documentElement.setAttribute("data-sidebar-image", "img-2");
                        break;
                    case "img-3":
                        getElementUsingTagname("data-sidebar-image", "img-3");
                        localStorage.setItem("data-sidebar-image", "img-3");
                        document.documentElement.setAttribute("data-sidebar-image", "img-3");
                        break;
                    case "img-4":
                        getElementUsingTagname("data-sidebar-image", "img-4");
                        localStorage.setItem("data-sidebar-image", "img-4");
                        document.documentElement.setAttribute("data-sidebar-image", "img-4");
                        break;
                    default:
                        if (localStorage.getItem("data-sidebar-image") && localStorage.getItem("data-sidebar-image") === "none") {
                            localStorage.setItem("data-sidebar-image", "none");
                            getElementUsingTagname("data-sidebar-image", "none");
                            document.documentElement.setAttribute("data-sidebar-image", "none");
                        } else if (localStorage.getItem("data-sidebar-image") === "img-1") {
                            localStorage.setItem("data-sidebar-image", "img-1");
                            getElementUsingTagname("data-sidebar-image", "img-1");
                            document.documentElement.setAttribute("data-sidebar-image", "img-2");
                        } else if (localStorage.getItem("data-sidebar-image") === "img-2") {
                            localStorage.setItem("data-sidebar-image", "img-2");
                            getElementUsingTagname("data-sidebar-image", "img-2");
                            document.documentElement.setAttribute("data-sidebar-image", "img-2");
                        } else if (localStorage.getItem("data-sidebar-image") === "img-3") {
                            localStorage.setItem("data-sidebar-image", "img-3");
                            getElementUsingTagname("data-sidebar-image", "img-3");
                            document.documentElement.setAttribute("data-sidebar-image", "img-3");
                        } else if (localStorage.getItem("data-sidebar-image") === "img-4") {
                            localStorage.setItem("data-sidebar-image", "img-4");
                            getElementUsingTagname("data-sidebar-image", "img-4");
                            document.documentElement.setAttribute("data-sidebar-image", "img-4");
                        }
                        break;
                }

                switch (isLayoutAttributes["data-layout-position"]) {
                    case "fixed":
                        getElementUsingTagname("data-layout-position", "fixed");
                        localStorage.setItem("data-layout-position", "fixed");
                        document.documentElement.setAttribute("data-layout-position", "fixed");
                        break;
                    case "scrollable":
                        getElementUsingTagname("data-layout-position", "scrollable");
                        localStorage.setItem("data-layout-position", "scrollable");
                        document.documentElement.setAttribute("data-layout-position", "scrollable");
                        break;
                    default:
                        if (localStorage.getItem("data-layout-position") && localStorage.getItem("data-layout-position") === "scrollable") {
                            getElementUsingTagname("data-layout-position", "scrollable");
                            localStorage.setItem("data-layout-position", "scrollable");
                            document.documentElement.setAttribute("data-layout-position", "scrollable");
                        } else {
                            getElementUsingTagname("data-layout-position", "fixed");
                            localStorage.setItem("data-layout-position", "fixed");
                            document.documentElement.setAttribute("data-layout-position", "fixed");
                        }
                        break;
                }

                switch (isLayoutAttributes["data-preloader"]) {
                    case "disable":
                        getElementUsingTagname("data-preloader", "disable");
                        localStorage.setItem("data-preloader", "disable");
                        document.documentElement.setAttribute("data-preloader", "disable");

                        break;
                    case "enable":
                        getElementUsingTagname("data-preloader", "enable");
                        localStorage.setItem("data-preloader", "enable");
                        document.documentElement.setAttribute("data-preloader", "enable");
                        let preloader = document.getElementById("preloader");
                        if (preloader) {
                            window.addEventListener("load", function () {
                                preloader.style.opacity = "0";
                                preloader.style.visibility = "hidden";
                            });
                        }
                        break;
                    default:
                        if (localStorage.getItem("data-preloader") && localStorage.getItem("data-preloader") === "disable") {
                            getElementUsingTagname("data-preloader", "disable");
                            localStorage.setItem("data-preloader", "disable");
                            document.documentElement.setAttribute("data-preloader", "disable");

                        } else if (localStorage.getItem("data-preloader") === "enable") {
                            getElementUsingTagname("data-preloader", "enable");
                            localStorage.setItem("data-preloader", "enable");
                            document.documentElement.setAttribute("data-preloader", "enable");
                            preloader = document.getElementById("preloader");
                            if (preloader) {
                                window.addEventListener("load", function () {
                                    preloader.style.opacity = "0";
                                    preloader.style.visibility = "hidden";
                                });
                            }
                        } else {
                            document.documentElement.setAttribute("data-preloader", "disable");
                        }
                        break;
                }

                switch (isLayoutAttributes["data-body-image"]) {
                    case "img-1":
                        getElementUsingTagname("data-body-image", "img-1");
                        localStorage.setItem("data-sidebabodyr-image", "img-1");
                        document.documentElement.setAttribute("data-body-image", "img-1");
                        if (document.getElementById("theme-settings-offcanvas")) {
                            document.documentElement.removeAttribute("data-sidebar-image");
                        }
                        break;
                    case "img-2":
                        getElementUsingTagname("data-body-image", "img-2");
                        localStorage.setItem("data-body-image", "img-2");
                        document.documentElement.setAttribute("data-body-image", "img-2");
                        break;
                    case "img-3":
                        getElementUsingTagname("data-body-image", "img-3");
                        localStorage.setItem("data-body-image", "img-3");
                        document.documentElement.setAttribute("data-body-image", "img-3");
                        break;
                    case "none":
                        getElementUsingTagname("data-body-image", "none");
                        localStorage.setItem("data-body-image", "none");
                        document.documentElement.setAttribute("data-body-image", "none");
                        break;

                    default:
                        if (localStorage.getItem("data-body-image") && localStorage.getItem("data-body-image") === "img-1") {
                            localStorage.setItem("data-body-image", "img-1");
                            getElementUsingTagname("data-body-image", "img-1");
                            document.documentElement.setAttribute("data-body-image", "img-1");

                            if (document.getElementById("theme-settings-offcanvas")) {
                                document.getElementById("sidebar-img").style.display = "none";
                                document.documentElement.removeAttribute("data-sidebar-image");
                            }
                        } else if (localStorage.getItem("data-body-image") === "img-2") {
                            localStorage.setItem("data-body-image", "img-2");
                            getElementUsingTagname("data-body-image", "img-2");
                            document.documentElement.setAttribute("data-body-image", "img-2");
                        } else if (localStorage.getItem("data-body-image") === "img-3") {
                            localStorage.setItem("data-body-image", "img-3");
                            getElementUsingTagname("data-body-image", "img-3");
                            document.documentElement.setAttribute("data-body-image", "img-3");
                        } else if (localStorage.getItem("data-body-image") === "none") {
                            localStorage.setItem("data-body-image", "none");
                            getElementUsingTagname("data-body-image", "none");
                            document.documentElement.setAttribute("data-body-image", "none");
                        }
                        break;
                }
            default:
                break;
        }
    }

    function initMenuItemScroll() {
        setTimeout(function () {
            var sidebarMenu = document.getElementById("navbar-nav");
            if (sidebarMenu) {
                var activeMenu = sidebarMenu.querySelector(".nav-item .active");
                var offset = activeMenu ? activeMenu.offsetTop : 0;
                if (offset > 300) {
                    var verticalMenu = document.getElementsByClassName("app-menu") ? document.getElementsByClassName("app-menu")[0] : "";
                    if (verticalMenu && verticalMenu.querySelector(".simplebar-content-wrapper")) {
                        setTimeout(function () {
                            offset === 330 ?
                                (verticalMenu.querySelector(".simplebar-content-wrapper").scrollTop = offset + 85) :
                                (verticalMenu.querySelector(".simplebar-content-wrapper").scrollTop = offset);
                        }, 0);
                    }
                }
            }
        }, 250);
    }

    // add change event listener on right layout setting
    function getElementUsingTagname(ele, val) {
        Array.from(document.querySelectorAll("input[name=" + ele + "]")).forEach(function (x) {
            val === x.value ? (x.checked = true) : (x.checked = false);

            x.addEventListener("change", function () {
                document.documentElement.setAttribute(ele, x.value);
                localStorage.setItem(ele, x.value);

                if (ele === "data-layout-width" && x.value === "boxed") {
                    document.documentElement.setAttribute("data-sidebar-size", "sm-hover");
                    localStorage.setItem("data-sidebar-size", "sm-hover");
                    document.getElementById("sidebar-size-small-hover").checked = true;
                } else if (ele === "data-layout-width" && x.value === "fluid") {
                    document.documentElement.setAttribute("data-sidebar-size", "lg");
                    localStorage.setItem("data-sidebar-size", "lg");
                    document.getElementById("sidebar-size-default").checked = true;
                }

                if (ele === "data-layout") {
                    if (x.value === "vertical") {
                        hideShowLayoutOptions("vertical");
                        isCollapseMenu();
                    } else if (x.value === "horizontal") {
                        if (document.getElementById("sidebarimg-none")) {
                            document.getElementById("sidebarimg-none").click();
                        }
                        hideShowLayoutOptions("horizontal");
                    } else if (x.value === "twocolumn") {
                        hideShowLayoutOptions("twocolumn");
                        document.documentElement.setAttribute("data-layout-width", "fluid");
                        document.getElementById("layout-width-fluid").click();
                        twoColumnMenuGenerate();
                        initTwoColumnActiveMenu();
                        isCollapseMenu();
                    } else if (x.value === "semibox") {
                        hideShowLayoutOptions("semibox");
                        document.documentElement.setAttribute("data-layout-width", "fluid");
                        document.getElementById("layout-width-fluid").click();
                        document.documentElement.setAttribute("data-layout-style", "default");
                        document.getElementById("sidebar-view-default").click();
                        isCollapseMenu();
                    }
                }

                if (ele === "data-preloader" && x.value === "enable") {
                    document.documentElement.setAttribute("data-preloader", "enable");
                    const preloader = document.getElementById("preloader");
                    if (preloader) {
                        setTimeout(function () {
                            preloader.style.opacity = "0";
                            preloader.style.visibility = "hidden";
                        }, 1000);
                    }
                    document.getElementById("customizerclose-btn").click();
                } else if (ele === "data-preloader" && x.value === "disable") {
                    document.documentElement.setAttribute("data-preloader", "disable");
                    document.getElementById("customizerclose-btn").click();
                }
            });
        });

        if (document.getElementById('collapseBgGradient')) {
            Array.from(document.querySelectorAll("#collapseBgGradient .form-check input")).forEach(function (subElem) {
                const myCollapse = document.getElementById('collapseBgGradient');
                if ((subElem.checked === true)) {
                    const bsCollapse = new bootstrap.Collapse(myCollapse, {
                        toggle: false,
                    });
                    bsCollapse.show()
                }

                if (document.querySelector("[data-bs-target='#collapseBgGradient']")) {
                    document.querySelector("[data-bs-target='#collapseBgGradient']").addEventListener('click', function (elem) {
                        document.getElementById("sidebar-color-gradient").click();
                    });
                }
            });
        }

        Array.from(document.querySelectorAll("[name='data-sidebar']")).forEach(function (elem) {
            if (document.querySelector("[data-bs-target='#collapseBgGradient']")) {
                if (document.querySelector("#collapseBgGradient .form-check input:checked")) {
                    document.querySelector("[data-bs-target='#collapseBgGradient']").classList.add("active");
                } else {
                    document.querySelector("[data-bs-target='#collapseBgGradient']").classList.remove("active");
                }

                elem.addEventListener("change", function () {
                    if (document.querySelector("#collapseBgGradient .form-check input:checked")) {
                        document.querySelector("[data-bs-target='#collapseBgGradient']").classList.add("active");
                    } else {
                        document.querySelector("[data-bs-target='#collapseBgGradient']").classList.remove("active");
                    }
                })
            }
        })

    }

    /**
     * Setzt die Standardattribute
     */
    function setDefaultAttribute() {
        let isLayoutAttributes;
        if (!localStorage.getItem("defaultAttribute")) {
            const attributesValue = document.documentElement.attributes;
            isLayoutAttributes = {};
            Array.from(attributesValue).forEach(function (x) {
                if (x && x.nodeName && x.nodeName !== "undefined") {
                    const nodeKey = x.nodeName;
                    isLayoutAttributes[nodeKey] = x.nodeValue;
                    localStorage.setItem(nodeKey, x.nodeValue);
                }
            });

            localStorage.setItem("defaultAttribute", JSON.stringify(isLayoutAttributes));
            layoutSwitch(isLayoutAttributes);

            // open right sidebar on first time load
            var offCanvas = document.querySelector('.btn[data-bs-target="#theme-settings-offcanvas"]');
            offCanvas ? offCanvas.click() : "";
        } else {
            isLayoutAttributes = {};
            isLayoutAttributes["data-layout"] = localStorage.getItem("data-layout");
            isLayoutAttributes["data-sidebar-size"] = localStorage.getItem("data-sidebar-size");
            isLayoutAttributes["data-bs-theme"] = localStorage.getItem("data-bs-theme");
            isLayoutAttributes["data-layout-width"] = localStorage.getItem("data-layout-width");
            isLayoutAttributes["data-sidebar"] = localStorage.getItem("data-sidebar");
            isLayoutAttributes['data-sidebar-image'] = localStorage.getItem('data-sidebar-image');
            isLayoutAttributes["data-layout-position"] = localStorage.getItem("data-layout-position");
            isLayoutAttributes["data-layout-style"] = localStorage.getItem("data-layout-style");
            isLayoutAttributes["data-topbar"] = localStorage.getItem("data-topbar");
            isLayoutAttributes["data-preloader"] = localStorage.getItem("data-preloader");
            isLayoutAttributes["data-body-image"] = localStorage.getItem("data-body-image");
            layoutSwitch(isLayoutAttributes);
        }
    }

    /**
     * Handelt das Umschalten auf den Vollbild Modus
     */
    function initFullScreen() {
        const fullscreenBtn = document.querySelector('[data-toggle="fullscreen"]');
        fullscreenBtn &&
        fullscreenBtn.addEventListener("click", function (e) {
            e.preventDefault();
            document.body.classList.toggle("fullscreen-enable");
            if (!document.fullscreenElement &&
                /* alternative standard method */
                !document.mozFullScreenElement &&
                !document.webkitFullscreenElement
            ) {
                // current working methods
                if (document.documentElement.requestFullscreen) {
                    document.documentElement.requestFullscreen();
                } else if (document.documentElement.mozRequestFullScreen) {
                    document.documentElement.mozRequestFullScreen();
                } else if (document.documentElement.webkitRequestFullscreen) {
                    document.documentElement.webkitRequestFullscreen(
                        Element.ALLOW_KEYBOARD_INPUT
                    );
                }
            } else {
                if (document.cancelFullScreen) {
                    document.cancelFullScreen();
                } else if (document.mozCancelFullScreen) {
                    document.mozCancelFullScreen();
                } else if (document.webkitCancelFullScreen) {
                    document.webkitCancelFullScreen();
                }
            }
        });

        document.addEventListener("fullscreenchange", exitHandler);
        document.addEventListener("webkitfullscreenchange", exitHandler);
        document.addEventListener("mozfullscreenchange", exitHandler);

        function exitHandler() {
            if (!document.webkitIsFullScreen && !document.mozFullScreen && !document.msFullscreenElement) {
                document.body.classList.remove("fullscreen-enable");
            }
        }
    }

    /**
     * Setzt den Layout-Modus
     */
    function setLayoutMode(mode, modeType, modeTypeId, html) {
        const isModeTypeId = document.getElementById(modeTypeId);
        html.setAttribute(mode, modeType);
        if (isModeTypeId) {
            document.getElementById(modeTypeId).click();
        }
    }

    /**
     * Hell und Dunkel Schalter für die Benutzeroberfläche. Einstellungen werden im localStorage gespeichert.
     */
    function initModeSetting() {
        const html = document.getElementsByTagName("HTML")[0];
        const lightDarkBtn = document.querySelectorAll(".light-dark-mode");
        if (lightDarkBtn && lightDarkBtn.length) {
            lightDarkBtn[0].addEventListener("click", function (event) {
                html.hasAttribute("data-bs-theme") && html.getAttribute("data-bs-theme") === "dark" ?
                    setLayoutMode("data-bs-theme", "light", "layout-mode-light", html) :
                    setLayoutMode("data-bs-theme", "dark", "layout-mode-dark", html);

                // Speichert die Einstellungen im localStorage
                html.hasAttribute("data-bs-theme") && html.getAttribute("data-bs-theme") === "dark" ?
                    localStorage.setItem("data-bs-theme", "dark") :
                    localStorage.setItem("data-bs-theme", "light");

            });
        }
    }

    function init() {
        setDefaultAttribute();
        twoColumnMenuGenerate();
        isCustomDropdown();
        isCustomDropdownResponsive();
        initFullScreen();
        initModeSetting();
        windowLoadContent();
        counter();
        initLeftMenuCollapse();
        initTopbarComponents();
        initComponents();
        pluginData();
        isCollapseMenu();
        initMenuItemScroll();
    }
    init();

    let timeOutFunctionId;

    function setResize() {
        const currentLayout = document.documentElement.getAttribute("data-layout");
        if (currentLayout !== "horizontal") {
            if (document.getElementById("navbar-nav")) {
                const simpleBar = new SimpleBar(document.getElementById("navbar-nav"));
                if (simpleBar) simpleBar.getContentElement();
            }

            if (document.getElementsByClassName("twocolumn-iconview")[0]) {
                const simpleBar1 = new SimpleBar(
                    document.getElementsByClassName("twocolumn-iconview")[0]
                );
                if (simpleBar1) simpleBar1.getContentElement();
            }
            clearTimeout(timeOutFunctionId);
        }
    }

    window.addEventListener("resize", function () {
        if (timeOutFunctionId) clearTimeout(timeOutFunctionId);
        timeOutFunctionId = setTimeout(setResize, 2000);
    });
})();


//
/********************* scroll top js ************************/
const mybutton = document.getElementById("back-to-top");

if (mybutton) {
    // When the user scrolls down 20px from the top of the document, show the button
    window.onscroll = function () {
        scrollFunction();
    };

    function scrollFunction() {
        if (document.body.scrollTop > 100 || document.documentElement.scrollTop > 100) {
            mybutton.style.display = "block";
        } else {
            mybutton.style.display = "none";
        }
    }

    // When the user clicks on the button, scroll to the top of the document
    function topFunction() {
        document.body.scrollTop = 0;
        document.documentElement.scrollTop = 0;
    }
}