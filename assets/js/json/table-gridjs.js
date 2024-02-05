import $ from 'jquery';
import { PluginPosition } from "gridjs";
import "gridjs/dist/theme/mermaid.css";

//GridJS
// $(document).ready(function() {
//     // grid.setData(JSON.parse(data|json_encode()));
//     // grid.setColumns(['ID', 'First Name', 'Last Name', 'Email', 'Enterprise', 'Phone', 'Country', 'Job']);

//    new Grid({

//         columns: ['ID', 'First Name', 'Last Name', 'Email', 'Enterprise', 'Phone', 'Country', 'Job'],
//         server: {
//             url: '{{ path("client_list_gridjs") }}',
//             then: data => data.data.map(c => [
//                 c.id,
//                 c.firstName,
//                 c.lastName,
//                 c.email,
//                 c.enterprise,
//                 c.phone,
//                 c.country,
//                 c.job,
//             ])
//         },
        
//     }).render(document.getElementById("table-gridjs-custom"));
// })