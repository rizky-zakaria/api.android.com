<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800"><?= $title; ?></h1>
    <h4>List User</h4>
    <table class="table table-hover">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Username</th>
                <th scope="col">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php $i = 1; ?>
            <?php foreach ($list as $r) : ?>
                <tr>
                    <th scope="row"><?= $i; ?></th>
                    <td><?= $r['name']; ?></td>
                    <td>
                        <?php
                        if ($r['is_active'] < 1) {
                            echo '<a href=" ' . base_url("Admin/activated?id=" . $r['id'] . "&&is_active=" . $r['is_active']) . "&&email=" . $r['email'] . ' " class="btn btn-danger">Aktifkan</a>';
                        } else {
                            echo '<a href=" ' . base_url("Admin/activated?id=" . $r['id'] . "&&is_active=" . $r['is_active']) . "&&email=" . $r['email'] . ' " class="btn btn-success">Matikan</a>';
                        }
                        ?>
                    </td>
                </tr>
                <?php $i++; ?>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<!-- /.container-fluid -->

</div>
<!-- End of Main Content -->