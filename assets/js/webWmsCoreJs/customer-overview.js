document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.querySelector(".search");
    const paginationEl = document.querySelector(".pagination.listjs-pagination");
    const prevBtn = document.querySelector(".pagination-prev");
    const nextBtn = document.querySelector(".pagination-next");
    const exportBtn = document.querySelector("#exportJsonBtn");

    const options = {
        valueNames: [
            "customerNr",
            "customerName",
            "customerAddressAddition",
            "customerAddressStreet",
            "customerAddressStreetNr",
            "customerCountryCode",
            "customerZipCode",
            "customerCity"
        ],
        pagination: true,
        page: 10,
        plugins: [
            ListPagination({
                left: 2,
                right: 2,
            }),
        ],
        item: `<tr>
            <th class="customerNr"></th>
            <th class="customerName"></th>
            <th class="customerAddressAddition"></th>
            <th class="customerAddressStreet"></th>
            <th class="customerAddressStreetNr"></th>
            <th class="customerCountryCode"></th>
            <th class="customerZipCode"></th>
            <th class="customerCity"></th>
            <td>
                <ul class="list-inline hstack gap-2 mb-0 d-flex justify-content-center">
                    <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-placement="top" title="">
                        <a href="#" class="view-customer text-primary d-inline-block">
                            <i class="mdi mdi-eye"></i>
                        </a>
                    </li>
                </ul>
            </td>
        </tr>`
    };

    const customerList = new List("customerList", options);

    let allCustomers = [];

    // Wareneingänge rendern
    function renderCustomerList(data) {
        allCustomers = data.map(customer => ({
            id: customer.customerId,
            customerNr: customer.customerNr,
            customerName: customer.customerName,
            customerAddressAddition: customer.customerAddressAddition,
            customerAddressStreet: customer.customerAddressStreet,
            customerAddressStreetNr: customer.customerAddressStreetNr,
            customerCountryCode: customer.customerCountryCode,
            customerZipCode: customer.customerZipCode,
            customerCity: customer.customerCity
        }));

        customerList.clear();
        customerList.add(allCustomers);
        updateViewLinks();
    }

    // View-Links
    function updateViewLinks() {
        document.querySelectorAll(".view-customer").forEach((link, index) => {
            if (allCustomers[index]) {
                link.href = `kunden_bearbeiten/customerId/${allCustomers[index].id}`;
            }
        });
    }

    // Init
    fetch("/customer_ajax")
        .then(response => response.json())
        .then(data => {
            renderCustomerList(data.data);
        })
        .catch(error => console.error("Fehler beim Laden der Kunden:", error));

    // Suche
    searchInput.addEventListener("input", function () {
        customerList.search(this.value.trim());
    });

    // Pagination Buttons
    nextBtn.addEventListener("click", e => {
        e.preventDefault();
        const next = paginationEl.querySelector(".active")?.nextElementSibling;
        next?.querySelector("a")?.click();
    });

    prevBtn.addEventListener("click", e => {
        e.preventDefault();
        const prev = paginationEl.querySelector(".active")?.previousElementSibling;
        prev?.querySelector("a")?.click();
    });

    exportBtn.addEventListener("click", function () {
        const exportData = customerList.items.map(item => item.values());
        const blob = new Blob([JSON.stringify(exportData, null, 2)], { type: "application/json" });
        const link = document.createElement("a");
        link.href = URL.createObjectURL(blob);
        link.download = "export_customer_overview.json";
        link.click();
    });

    document.addEventListener("DOMContentLoaded", function () {
        document.addEventListener("click", function (e) {
            if (e.target.matches("#save-btn")) {
                const form = document.querySelector("#customer-form-new");
                modal.submitForm(form, "/kunden_anlegen", "Kunde gespeichert", "Fehler beim Speichern", () => {
                    // Optional: z.B. Liste neu laden
                    customerList.update();
                    customerList.sort('customerNr', { order: 'desc' });
                });
            }
        });
    });
});