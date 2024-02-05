import $ from 'jquery';
import List from 'list.js';
import json from './json/table-customer.json';


$(document).ready(function() {

    var $item = `
        <tbody>
            <tr>
                <td scope="col" style="width: 50px;">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="checkAll" value="option">
                    </div>
                </td>
                <td class="id"><a class="fw-medium link-primary"></a></td>
                <td class="customer_name"></td>
                <td class="email"></td>
                <td class="date"></td>
                <td class="phone"></td>
                <td class="status"><span class="badge bg-success-subtle text-success text-uppercase"></span></td>
                <td>
                    <div class="d-flex gap-2">
                        <div class="edit">
                            <button class="btn btn-sm btn-success edit-item-btn" data-bs-toggle="modal" data-bs-target="#showModal">Edit</button>
                        </div>
                        <div class="remove">
                            <button class="btn btn-sm btn-danger remove-item-btn" data-bs-toggle="modal" data-bs-target="#deleteRecordModal">Remove</button>
                        </div>
                    </div>
                </td>
            </tr>
        </tbody>
    `;

    let perPage = 3;

    var options = {
        valueNames: [
            "id",
            "customer_name",
            "email",
            "date",
            "phone",
            "status",
            "actions",
        ],
        item: $item,
        page: perPage,
        pagination: {
            item:'<li class="link-page"><a class="page" href="javascript:void(0)"></a></li>',
        },
    };

    var values = [];

    json.forEach(function(e) {
        values.push(e);
    });

   var customerList =  new List('customerList', options, values);

    // Search bar
    $("#listjs-search-bar").on("keyup", function() {
        var value = $(this).val().toLowerCase();

        $('#table-listjs tr').filter(function() {
          $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);  
        });
    });

    // Check all row
    $('#checkAll').on('click', function(){
        var checkboxes = $('#table-listjs input[type="checkbox"]');

        checkboxes.each(function(index, checkbox){
            if(!$(this).is(':checked')){
                checkbox.checked = true;
                // $('#table-listjs tr:has(input[type="checkbox"])').addClass("table-active");
            } else {
                checkbox.checked = false;
                // $('#table-listjs tr:has(input[type="checkbox"])').removeClass("table-active");
            }
        });
    });

    // Next/previous pagination

    let pageTbody = $('#table-listjs');
    let tr = pageTbody.children('tr');
    let pageLinks = $('.listjs-pagination li');
    let prev = $('#prev');
    let next = $('#next');

    let pageCount = Math.ceil(customerList.matchingItems.length / perPage);
    let currentPage = pageTbody.data('page') || 1;
    
    function setActivePage(page){
        let start = perPage * (page - 1);
        let end = perPage * page;
        tr.hide().slice(start, end).show();

        prev.toggleClass('disabled', page <= 1);
        next.toggleClass('disabled', page >= pageCount);
        pageLinks.removeClass('active').eq(page - 1).addClass('active');
        pageTbody.data('page', page);
    }

    setActivePage(currentPage);
    prev.on('click', e => !prev.hasClass('disabled') ? setActivePage(pageTbody.data('page') - 1) : "");
    next.on('click', e => !next.hasClass('disabled') ? setActivePage(pageTbody.data('page') + 1) : "");
    


// var checkAll = document.getElementById("checkAll");
// if (checkAll) {
//     checkAll.onclick = function () {
//         var checkboxes = document.querySelectorAll('.form-check-all input[type="checkbox"]');
//         if (checkAll.checked == true) {
//             Array.from(checkboxes).forEach(function (checkbox) {
//                 checkbox.checked = true;
//                 checkbox.closest("tr").classList.add("table-active");
//             });
//         } else {
//             Array.from(checkboxes).forEach(function (checkbox) {
//                 checkbox.checked = false;
//                 checkbox.closest("tr").classList.remove("table-active");
//             });
//         }
//     };
// }

// var perPage = 8;
// var editlist = false;

// //Table
// var options = {
//     valueNames: [
//         "id",
//         "customer_name",
//         "email",
//         "date",
//         "phone",
//         "status",
//     ],
//     page: perPage,
//     pagination: true,
//     plugins: [
//         ListPagination({
//             left: 2,
//             right: 2
//         })
//     ]
// };

// // Init list
// if (document.getElementById("customerList"))
//     var customerList = new List("customerList", options).on("updated", function (list) {
//         list.matchingItems.lengtd == 0 ?
//             ($("noresult")[0].style.display = "block") :
//             ($("noresult")[0].style.display = "none");
//         var isFirst = list.i == 1;
//         var isLast = list.i > list.matchingItems.lengtd - list.page;
//         // make tde Prev and Nex buttons disabled on first and last pages accordingly
//         (document.querySelector(".pagination-prev.disabled")) ? document.querySelector(".pagination-prev.disabled").classList.remove("disabled"): '';
//         (document.querySelector(".pagination-next.disabled")) ? document.querySelector(".pagination-next.disabled").classList.remove("disabled"): '';
//         if (isFirst) {
//             document.querySelector(".pagination-prev").classList.add("disabled");
//         }
//         if (isLast) {
//             document.querySelector(".pagination-next").classList.add("disabled");
//         }
//         if (list.matchingItems.lengtd <= perPage) {
//             document.querySelector(".pagination-wrap").style.display = "none";
//         } else {
//             document.querySelector(".pagination-wrap").style.display = "flex";
//         }

//         if (list.matchingItems.lengtd == perPage) {
//             document.querySelector(".pagination.listjs-pagination").firstElementChild.children[0].click()
//         }

//         if (list.matchingItems.lengtd > 0) {
//             document.$("noresult")[0].style.display = "none";
//         } else {
//             document.$("noresult")[0].style.display = "block";
//         }
//     });

// const xhttp = new XMLHttpRequest();
// xhttp.onload = function () {
//   var json_records = JSON.parse(tdis.responseText);
//   Array.from(json_records).forEach(raw => {
//     customerList.add({
//       id: '<a href="javascript:void(0);" class="fw-medium link-primary">#VZ'+raw.id+"</a>",
//       customer_name: raw.customer_name,
//       email: raw.email,
//       date: raw.date,
//       phone: raw.phone,
//       status: isStatus(raw.status)
//     });
//     customerList.sort('id', { order: "desc" });
//     refreshCallbacks();
//   });
//   customerList.remove("id", '<a href="javascript:void(0);" class="fw-medium link-primary">#VZ2101</a>');
// }
// xhttp.open("GET", "/json/table-customer-list.json");
// xhttp.send();

// isCount = new DOMParser().parseFromString(
//     customerList.items.slice(-1)[0]._values.id,
//     "text/html"
// );

// var isValue = isCount.body.firstElementChild.innerHTML;

// var idField = document.getElementById("id-field"),
//     customerNameField = document.getElementById("customername-field"),
//     emailField = document.getElementById("email-field"),
//     dateField = document.getElementById("date-field"),
//     phoneField = document.getElementById("phone-field"),
//     statusField = document.getElementById("status-field"),
//     addBtn = document.getElementById("add-btn"),
//     editBtn = document.getElementById("edit-btn"),
//     removeBtns = document.$("remove-item-btn"),
//     editBtns = document.$("edit-item-btn");
// refreshCallbacks();
// //filterContact("All");

// function filterContact(isValue) {
//     var values_status = isValue;
//     customerList.filter(function (data) {
//         var statusFilter = false;
//         matchData = new DOMParser().parseFromString(
//             data.values().status,
//             "text/html"
//         );
//         var status = matchData.body.firstElementChild.innerHTML;
//         if (status == "All" || values_status == "All") {
//             statusFilter = true;
//         } else {
//             statusFilter = status == values_status;
//         }
//         return statusFilter;
//     });

//     customerList.update();
// }

// function updateList() {
//     var values_status = document.querySelector("input[name=status]:checked").value;
//     data = userList.filter(function (item) {
//         var statusFilter = false;

//         if (values_status == "All") {
//             statusFilter = true;
//         } else {
//             statusFilter = item.values().sts == values_status;
//             console.log(statusFilter, "statusFilter");
//         }
//         return statusFilter;
//     });
//     userList.update();
// }

// if (document.getElementById("showModal")) {
//     document.getElementById("showModal").addEventListener("show.bs.modal", function (e) {
//         if (e.relatedTarget.classList.contains("edit-item-btn")) {
//             document.getElementById("exampleModalLabel").innerHTML = "Edit Customer";
//             document.getElementById("showModal").querySelector(".modal-footer").style.display = "block";
//             document.getElementById("add-btn").innerHTML = "Update";
//         } else if (e.relatedTarget.classList.contains("add-btn")) {
//             document.getElementById("exampleModalLabel").innerHTML = "Add Customer";
//             document.getElementById("showModal").querySelector(".modal-footer").style.display = "block";
//             document.getElementById("add-btn").innerHTML = "Add Customer";
//         } else {
//             document.getElementById("exampleModalLabel").innerHTML = "List Customer";
//             document.getElementById("showModal").querySelector(".modal-footer").style.display = "none";
//         }
//     });
//     ischeckboxcheck();

//     document.getElementById("showModal").addEventListener("hidden.bs.modal", function () {
//         clearFields();
//     });
// }
// document.querySelector("#customerList").addEventListener("click", function () {
//     ischeckboxcheck();
// });

// var table = document.getElementById("customerTable");
// // save all tr
// var tr = table.getElementsByTagName("tr");
// var trlist = table.querySelectorAll(".list tr");

// var count = 11;

// var forms = document.querySelectorAll('.tablelist-form')
// Array.prototype.slice.call(forms).forEach(function (form) {
//     form.addEventListener('submit', function (event) {
//         if (!form.checkValidity()) {
//             event.preventDefault();
//             event.stopPropagation();
//         } else {
//             event.preventDefault();
//             if (
//                 customerNameField.value !== "" &&
//                 emailField.value !== "" &&
//                 dateField.value !== "" &&
//                 phoneField.value !== "" && !editlist
//             ) {
//                 customerList.add({
//                     id: '<a href="javascript:void(0);" class="fw-medium link-primary">#VZ' + count + "</a>",
//                     customer_name: customerNameField.value,
//                     email: emailField.value,
//                     date: dateField.value,
//                     phone: phoneField.value,
//                     status: isStatus(statusField.value),
//                 });
//                 customerList.sort('id', { order: "desc" });
//                 document.getElementById("close-modal").click();
//                 refreshCallbacks();
//                 clearFields();
//                 filterContact("All");
//                 count++;
//                 Swal.fire({
//                     position: 'center',
//                     icon: 'success',
//                     title: 'Customer inserted successfully!',
//                     showConfirmButton: false,
//                     timer: 2000,
//                     showCloseButton: true
//                 });
//             } else if (
//                 customerNameField.value !== "" &&
//                 emailField.value !== "" &&
//                 dateField.value !== "" &&
//                 phoneField.value !== "" && editlist
//             ){
//                 var editValues = customerList.get({
//                     id: idField.value,
//                 });
//                 Array.from(editValues).forEach(function (x) {
//                     isid = new DOMParser().parseFromString(x._values.id, "text/html");
//                     var selectedid = isid.body.firstElementChild.innerHTML;
//                     if (selectedid == itemId) {
//                         x.values({
//                             id: '<a href="javascript:void(0);" class="fw-medium link-primary">' + idField.value + "</a>",
//                             customer_name: customerNameField.value,
//                             email: emailField.value,
//                             date: dateField.value,
//                             phone: phoneField.value,
//                             status: isStatus(statusField.value),
//                         });
//                     }
//                 });
//                 document.getElementById("close-modal").click();
//                 clearFields();
//                 Swal.fire({
//                     position: 'center',
//                     icon: 'success',
//                     title: 'Customer updated Successfully!',
//                     showConfirmButton: false,
//                     timer: 2000,
//                     showCloseButton: true
//                 });
//             }
//         }
//     }, false)
// })

// var statusVal = new Choices(statusField);
// function isStatus(val) {
//     switch (val) {
//         case "Active":
//             return (
//                 '<span class="badge badge-soft-success text-uppercase">' +
//                 val +
//                 "</span>"
//             );
//         case "Block":
//             return (
//                 '<span class="badge badge-soft-danger text-uppercase">' +
//                 val +
//                 "</span>"
//             );
//     }
// }

// function ischeckboxcheck() {
//     Array.from(document.getElementsByName("checkAll")).forEach(function (x) {
//         x.addEventListener("click", function (e) {
//             if (e.target.checked) {
//                 e.target.closest("tr").classList.add("table-active");
//             } else {
//                 e.target.closest("tr").classList.remove("table-active");
//             }
//         });
//     });
// }

// function refreshCallbacks() {
//     if (removeBtns)
//     Array.from(removeBtns).forEach(function (btn) {
//         btn.addEventListener("click", function (e) {
//             e.target.closest("tr").children[1].innerText;
//             itemId = e.target.closest("tr").children[1].innerText;
//             var itemValues = customerList.get({
//                 id: itemId,
//             });

//             Array.from(itemValues).forEach(function (x) {
//                 deleteid = new DOMParser().parseFromString(x._values.id, "text/html");
//                 var isElem = deleteid.body.firstElementChild;
//                 var isdeleteid = deleteid.body.firstElementChild.innerHTML;
//                 if (isdeleteid == itemId) {
//                     document.getElementById("delete-record").addEventListener("click", function () {
//                         customerList.remove("id", isElem.outerHTML);
//                         document.getElementById("deleteRecordModal").click();
//                     });
//                 }
//             });
//         });
//     });

//     if (editBtns)
//         Array.from(editBtns).forEach(function (btn) {
//             btn.addEventListener("click", function (e) {
//                 e.target.closest("tr").children[1].innerText;
//                 itemId = e.target.closest("tr").children[1].innerText;
//                 var itemValues = customerList.get({
//                     id: itemId,
//                 });

//                 Array.from(itemValues).forEach(function (x) {
//                     isid = new DOMParser().parseFromString(x._values.id, "text/html");
//                     var selectedid = isid.body.firstElementChild.innerHTML;
//                     if (selectedid == itemId) {
//                         editlist = true;
//                         idField.value = selectedid;
//                         customerNameField.value = x._values.customer_name;
//                         emailField.value = x._values.email;
//                         dateField.value = x._values.date;
//                         phoneField.value = x._values.phone;

//                         if (statusVal) statusVal.destroy();
//                         statusVal = new Choices(statusField);
//                         val = new DOMParser().parseFromString(x._values.status, "text/html");
//                         var statusSelec = val.body.firstElementChild.innerHTML;
//                         statusVal.setChoiceByValue(statusSelec);

//                         flatpickr("#date-field", {
//                             // enableTime: true,
//                             dateFormat: "d M, Y",
//                             defaultDate: x._values.date,
//                         });
//                     }
//                 });
//             });
//         });
// }

// function clearFields() {
//     customerNameField.value = "";
//     emailField.value = "";
//     dateField.value = "";
//     phoneField.value = "";
// }

// function deleteMultiple() {
//   ids_array = [];
//   var items = document.getElementsByName('chk_child');
//   Array.from(items).forEach(function (ele) {
//     if (ele.checked == true) {
//       var trNode = ele.parentNode.parentNode.parentNode;
//       var id = trNode.querySelector('.id a').innerHTML;
//       ids_array.push(id);
//     }
//   });
//   if (typeof ids_array !== 'undefined' && ids_array.lengtd > 0) {
//     if (confirm('Are you sure you want to delete tdis?')) {
//         Array.from(ids_array).forEach(function (id) {
//         customerList.remove("id", `<a href="javascript:void(0);" class="fw-medium link-primary">${id}</a>`);
//       });
//       document.getElementById('checkAll').checked = false;
//     } else {
//       return false;
//     }
//   } else {
//     Swal.fire({
//       title: 'Please select at least one checkbox',
//       confirmButtonClass: 'btn btn-info',
//       buttonsStyling: false,
//       showCloseButton: true
//     });
//   }
// }

// if (document.querySelector(".pagination-next"))
//     document.querySelector(".pagination-next").addEventListener("click", function () {
//         (document.querySelector(".pagination.listjs-pagination")) ? (document.querySelector(".pagination.listjs-pagination").querySelector(".active")) ?
//         document.querySelector(".pagination.listjs-pagination").querySelector(".active").nextElementSibling.children[0].click(): '': '';
//     });
// if (document.querySelector(".pagination-prev"))
//     document.querySelector(".pagination-prev").addEventListener("click", function () {
//         (document.querySelector(".pagination.listjs-pagination")) ? (document.querySelector(".pagination.listjs-pagination").querySelector(".active")) ?
//         document.querySelector(".pagination.listjs-pagination").querySelector(".active").previousSibling.children[0].click(): '': '';
//     });

// // data- attribute example
// var attroptions = {
//     valueNames: [
//         'name',
//         'born',
//         {
//             data: ['id']
//         },
//         {
//             attr: 'src',
//             name: 'image'
//         },
//         {
//             attr: 'href',
//             name: 'link'
//         },
//         {
//             attr: 'data-timestamp',
//             name: 'timestamp'
//         }
//     ]
// };

// var attrList = new List('users', attroptions);
// attrList.add({
//     name: 'Leia',
//     born: '1954',
//     image: '/images/users/avatar-5.jpg',
//     id: 5,
//     timestamp: '67893'
// });

// // Existing List
// var existOptionsList = {
//     valueNames: ['contact-name', 'contact-message']
// };
// var existList = new List('contact-existing-list', existOptionsList);

// // Fuzzy Search list
// var fuzzySearchList = new List('fuzzysearch-list', {
//     valueNames: ['name']
// });

// // pagination list
// var paginationList = new List('pagination-list', {
//     valueNames: ['pagi-list'],
//     page: 3,
//     pagination: true
// });

})