import "./bootstrap";
import Alpine from "alpinejs";
import Swal from "sweetalert2";
import { confirmDelete } from "./helpers/confirmDelete";

window.Alpine = Alpine;

Alpine.start();

window.Swal = Swal;
window.confirmDelete = confirmDelete;
