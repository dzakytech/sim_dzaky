    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#sidebarToggle').click(function() {
                $('#sidebar').toggleClass('active');
                if($('#sidebar').hasClass('active')) {
                    $('#sidebar').css('left', '-250px');
                    $('#content').css('margin-left', '0');
                    $('.navbar').css('margin-left', '0');
                } else {
                    $('#sidebar').css('left', '0');
                    $('#content').css('margin-left', '250px');
                    $('.navbar').css('margin-left', '250px');
                }
            });
        });
    </script>
</body>
</html>
