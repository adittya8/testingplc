window.screenSizes = { sm: 576, md: 768, lg: 992, xl: 1200 };
window.colors = {
    primary: "#10b981",
    secondary: "#64748b",
    success: "#10b981",
    warning: "#fb7720",
    danger: "#cb1515",
};

window.Axios = axios;
window.Axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";

window.NotiflixJs = Notiflix;
window.confirm = Notiflix.Confirm;
NotiflixJs.Confirm.init({
    borderRadius: ".75rem",
    okButtonBackground: colors.success,
    okButtonColor: "#fff",
    cancelButtonBackground: colors.secondary,
    cancelButtonColor: "#fff",
    buttonsFontSize: "1rem",
    messageMaxLength: 500,
});

window.Bootstrap = bootstrap;

window.activateTooltips = () => {
    if (window.innerWidth < 992) return;

    const tooltipTriggerList = document.querySelectorAll(
        '[data-bs-toggle="tooltip"]'
    );

    const tooltipList = [...tooltipTriggerList].map(
        (tooltipTriggerEl) => new bootstrap.Tooltip(tooltipTriggerEl)
    );

    const customTooltips = document.querySelectorAll("[data-bs-tooltip]");
    customTooltips.forEach((ctt) => {
        new bootstrap.Tooltip(ctt, {});
    });
};

window.init = (form = null) => {
    activateTooltips();
};

init();

/**
 * Handles sidebar submenu dropdown slide up/down
 */
const sidebarDropdownItems = document.querySelectorAll(".has-submenu");
sidebarDropdownItems.forEach((el) => {
    el.querySelector(":scope > .nav-link").addEventListener("click", (e) => {
        e.stopPropagation();
        const subMenu = el.querySelector(":scope > .sidebar-submenu");

        if (el.classList.contains("open")) {
            let curHeight = subMenu.scrollHeight;
            subMenu.style.height = "0";
            el.classList.remove("open");

            if (el.parentElement.classList.contains("sidebar-submenu")) {
                updateSubmenuParentHeight(el.parentElement, -curHeight);
            }
        } else {
            let height = subMenu.scrollHeight;
            subMenu.style.height = `${height}px`;
            el.classList.add("open");

            if (el.parentElement.classList.contains("sidebar-submenu")) {
                updateSubmenuParentHeight(el.parentElement, height);
            }
        }
    });
});

/**
 * Increases or decreases the height of the parent submenu of a submenu
 * that is toggled visible or invisible
 * @param {HTMLElement} parentSubMenu
 * @param {number} height
 */
function updateSubmenuParentHeight(parentSubMenu, height) {
    let newHeight = parentSubMenu.scrollHeight + height;

    parentSubMenu.style.height = `${newHeight}px`;

    const parentOfParent = parentSubMenu.parentElement.parentElement;
    if (parentOfParent.classList.contains("sidebar-submenu")) {
        updateSubmenuParentHeight(parentOfParent, height);
    }
}

/**
 * Toggles the sidebar
 */
document.querySelector("#sidebarToggler")?.addEventListener("click", () => {
    document.body.classList.toggle("sidebar-toggle");

    if (window.innerWidth < 992) {
        const backdrop = addBackdrop(
            document.defaultView
                .getComputedStyle(document.querySelector(".sidebar"))
                .getPropertyValue("z-index") - 1
        );

        backdrop.addEventListener("click", (e) => {
            document.body.classList.remove("sidebar-toggle");
            backdrop.remove();
        });
    }
});

/**
 * Avoids hiding dropdown menu when click inside and the target is not a link
 */
document.querySelectorAll(".dropdown-menu").forEach((el) => {
    el.addEventListener("click", (e) => {
        if (e.target.nodeName != "A") {
            e.stopPropagation();
        }
    });
});

/**
 * Adds backdrop to the passed element
 * @param {number} zIndex
 * @param {HTMLElement|null} element
 * @returns {HTMLDivElement}
 */
window.addBackdrop = (zIndex, element = null) => {
    const backdrop = document.createElement("div");
    backdrop.classList.add("backdrop");
    backdrop.style.zIndex = zIndex;

    if (element) {
        element.appendChild(backdrop);

        return backdrop;
    }

    document.body.appendChild(backdrop);

    return backdrop;
};

/**
 * Adds a loading animation inside an element
 * @param {string|HTMLElement} parentElement
 * @param {boolean} backdrop
 */
window.addLoader = (
    parentElement,
    { backdrop = true, color = "#fff", align = "center" } = {}
) => {
    if (!(parentElement instanceof HTMLElement)) {
        parentElement = document.querySelector(parentElement);
    }

    const loader = document.createElement("div");
    loader.classList.add("ripple");
    const inner1 = document.createElement("div");
    const inner2 = document.createElement("div");
    inner1.style.backgroundColor = color;
    inner2.style.backgroundColor = color;
    if (align == "center") {
        loader.style.display = "block";
        loader.style.margin = "auto";
    }
    loader.append(inner1, inner2);

    if (backdrop) {
        const backdrop = document.createElement("div");
        backdrop.className = "loader backdrop";
        backdrop.style.zIndex = 1000001;

        backdrop.append(loader);
        parentElement.append(backdrop);
        return;
    }

    loader.classList.add("loader");
    parentElement.append(loader);
};

/**
 * Removes the animation inside an element
 * @param {string|HTMLElement} parentElement
 */
window.removeLoader = (parentElement) => {
    if (!(parentElement instanceof HTMLElement)) {
        parentElement = document.querySelector(parentElement);
    }

    parentElement.querySelector(":scope > .loader").remove();
};

window.openModal = (route, title) => {
    const formModal = new bootstrap.Modal("#formModal", {});
    const formModalBody = document.querySelector("#formModalBody");
    const formModalTitle = document.querySelector("#formModalTitle");

    formModalTitle.innerHTML = title;
    formModalBody.innerHTML = "";
    addLoader(formModalBody, {
        color: "#000",
        align: "center",
        backdrop: false,
    });
    formModal.show();

    axios
        .get(route)
        .then((response) => {
            formModalBody.innerHTML = response.data;
            init(formModalBody.querySelector("form"));
        })
        .catch((error) => {
            formModalBody.innerHTML =
                error.response &&
                error.response.data &&
                error.response.data.message
                    ? `<div class="text-danger">${error.response.data.message}</div>`
                    : `<div class="text-danger">Something went wrong!</div>`;
        });
};

/**
 * Shows server side error messages on forms
 * @param {HTMLFormElement} form - The form object
 * @param {JSON} errors - JSON Response in Laravel's error response format
 */
window.handleSubmitError = (form, response) => {
    // Remove previous error messages and classes
    form.querySelectorAll(".alert-danger").forEach((el) => el.remove());
    form.querySelectorAll(".text-danger").forEach((el) => el.remove());
    form.querySelectorAll(".is-invalid").forEach((el) =>
        el.classList.remove("is-invalid")
    );

    let messages = [];
    if (response && response.errors) {
        for (const key in response.errors) {
            const input = form.querySelector(`[name="${key}"]`);
            if (!input) {
                messages.push(response.errors[key]);
                continue;
            }

            input.classList.add("is-invalid");
            input.insertAdjacentHTML(
                "afterend",
                `<div class="text-danger">${response.errors[key]}</div>`
            );
        }
    } else if (response.message) {
        const div = document.createElement("div");
        div.classList.add("col-12");
        const alert = document.createElement("div");
        alert.classList.add("alert", "alert-danger", "mb-0");
        alert.innerText = response.message;
        div.append(alert);
        form.prepend(div);
    }

    if (messages.length > 0) {
        const div = document.createElement("div");
        div.classList.add("col-12");
        const alert = document.createElement("div");
        alert.classList.add("alert", "alert-danger", "mb-0");
        const ul = document.createElement("ul");
        ul.classList.add("m-0");

        messages.forEach((msg) => {
            const li = document.createElement("li");
            li.innerText = msg;
            ul.append(li);
        });

        alert.append(ul);
        div.append(alert);
        form.prepend(div);
    }
};

/**
 *
 * @param {Event} e
 * @param {HTMLFormElement} form
 */
window.handleFormSubmit = async (e, form) => {
    e.preventDefault();

    addLoader(document.body, { backdrop: true, align: "center" });

    await axios({
        method: form.method,
        url: form.action,
        data: new FormData(form),
        headers: { Accept: "application/json" },
    })
        .then((response) => {
            localStorage.setItem("session_success", response.data.message);
            location.reload();
        })
        .catch((error) => {
            console.error(error);
            removeLoader(document.body);
            handleSubmitError(form, error.response.data);
        });
};

/**
 *
 * @param {string} content - HTML as string
 * @param {string} type - Bootstrap theme color type
 * @param {number} duration
 */
window.showToast = (content, type = "success", duration = 7000) => {
    let container = document.querySelector(".floating-message-container");
    if (!container) {
        container = document.createElement("div");
        container.className = "floating-message-container";
        document.body.appendChild(container);
    }

    const alert = document.createElement("div");
    alert.className = `floating-message floating-message-${type}`;

    let icon = "";
    switch (type) {
        case "success":
            icon = '<i class="fa-solid fa-check-circle"></i>';
            break;
        case "danger":
            icon = '<i class="fa-solid fa-exclamation-circle"></i>';
            break;
        case "warning":
            icon = '<i class="fa-solid fa-exclamation-circle"></i>';
            break;
        default:
            icon = '<i class="fa-solid fa-circle-info"></i>';
    }

    content = `<div>${content}</div>`;

    const closeButtonHTML = `
    <button href="button" aria-label="Close" class="close-btn"><i class="fa-solid fa-xmark"></i></button>
  `;

    alert.innerHTML = `${icon} ${content} ${closeButtonHTML}`;
    container.append(alert);

    setTimeout(() => {
        removeToast(alert, container);
    }, duration);

    alert.querySelector(".close-btn").addEventListener("click", () => {
        removeToast(alert, container);
    });
};

/**
 *
 * @param {HTMLElement} alert
 * @param {HTMLElement} container
 */
function removeToast(alert, container) {
    alert.classList.add("remove");
    setTimeout(() => {
        alert.remove();

        if (!document.querySelector(".floating-message")) {
            container.remove();
        }
    }, 300);
}

const navbarDropdownToggle = document.querySelector("[data-open-navbar]");
navbarDropdownToggle?.addEventListener("click", (event) => {
    event.stopPropagation();
    const navbar = document.querySelector(".navbar-collapsible");
    console.log("ss");
    navbar.classList.toggle("show");
});

document.addEventListener("mouseup", function (event) {
    const navbar = document.querySelector(".navbar-collapsible");
    // if (!event.target.matches("navbar-toggle")) {
    navbar.classList.remove("show");
    // }
});

window.deleteItem = (url, id, itemType, additionalMsg = null) => {
    confirm.show(
        "Confirm Delete",
        `Are you sure to delete this ${itemType}?${
            additionalMsg ? " " + additionalMsg : ""
        }`,
        "Yes",
        "No",
        () => {
            addLoader(document.body, { backdrop: true, align: "center" });
            const form = document.createElement("form");
            form.action = url;
            form.method = "POST";
            const token = document.querySelector(
                'meta[name="csrf-token"]'
            ).content;
            form.innerHTML = `
                <input type="hidden" name="_method" value="DELETE">
                <input type="hidden" name="_token" value="${token}" />
            `;
            form.classList.add("d-none");
            document.body.append(form);
            form.submit();
            form.remove();
            return;
        },
        () => {},
        {
            titleColor: colors.danger,
        }
    );
};
