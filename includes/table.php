</main>
</div>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://cdn.datatables.net/2.0.3/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/2.0.3/js/dataTables.bootstrap5.js"></script>

<script>
    $(document).ready(function () {
        let table = $('.card-body table').addClass('table table-hover table-striped').DataTable({
            responsive: true,
            language: {
 
                search: "Search:",
                lengthMenu: "Show _MENU_ entries",
                info: "Showing _START_ to _END_ of _TOTAL_ entries",

                paginate: {
                   
                    previous: "&lsaquo;",  
                    next: "&rsaquo;"       
                }
            },
            columnDefs: [
                { orderable: false, targets: -1 }
            ]
        });
    });
</script>
</body>

</html>