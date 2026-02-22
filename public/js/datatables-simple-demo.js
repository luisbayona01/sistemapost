window.addEventListener('DOMContentLoaded', event => {
    // Simple-DataTables
    // https://github.com/fiduswriter/Simple-DataTables/wiki

    const datatablesSimple = document.getElementById('datatablesSimple');
    if (datatablesSimple) {
        new simpleDatatables.DataTable(datatablesSimple, {
            searchable: true,
            fixedHeight: true,
            perPage: 25,
            labels: {
                placeholder: "Buscar productos...",
                perPage: "Items por página",
                noRows: "No hay productos encontrados",
                info: "Mostrando {start} a {end} de {rows} productos",
            }
        });
    }
});
