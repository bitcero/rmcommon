<h1><?php _e('Color Test for Two&bull;P6', 'rmcommon'); ?></h1>
<hr>

<div class="row">

    <div class="col-xs-12">
        <h2><strong><?php _e('Buttons', 'rmcommon'); ?></strong></h2>

        <button type="button" class="btn btn-default">btn-default</button>
        <button type="button" class="btn btn-info">btn-info</button>
        <button type="button" class="btn btn-success">btn-success</button>
        <button type="button" class="btn btn-warning">btn-warning</button>
        <button type="button" class="btn btn-danger">btn-danger</button>
        <button type="button" class="btn btn-primary">btn-primary</button>
        <button type="button" class="btn btn-link">btn-link</button>
    </div>

</div>

<div class="row">
    <div class="col-xs-12">
        <h2><strong><?php _e('Form Controls', 'rmcommon'); ?></strong></h2>
    </div>
</div>
<div class="row">

    <div class="col-sm-4">
        <input type="text" class="form-control" placeholder=".form-control">
    </div>
    <div class="col-sm-4">
        <select class="form-control" placeholder=".form-control">
            <option value="">.form-control</option>
        </select>
    </div>
    <div class="col-sm-4">
        <label class="checkbox"><input type="checkbox" checked> Checkbox</label>
        <label class="radio"><input type="radio" checked> Radio</label>
    </div>

</div>
<div class="row">
    <div class="col-xs-12">
        <h2><strong><?php _e('Info boxes', 'rmcommon'); ?></strong></h2>
        <div class="cu-box">
            <div class="box-header">
                <span class="fa fa-caret-up box-handler"></span>
                <h3><?php _e('Title', 'rmcommon'); ?></h3>
            </div>
            <div class="box-content collapsable">
                <?php _e('Content fo the box', 'rmcommon'); ?>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xs-12">
        <h2><strong><?php _e('Modals', 'rmcommon'); ?></strong></h2>
        <button class="btn btn-info" data-toggle="modal" data-target="#myModal">
            <?php _e('Launch demo modal', 'rmcommon'); ?>
        </button>
        <button class="btn btn-info" data-toggle="modal" data-target="#MyCuModal">
            <?php _e('Launch demo modal 2', 'rmcommon'); ?>
        </button>

    </div>
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel"><?php _e('Modal title', 'rmcommon'); ?></h4>
                </div>
                <div class="modal-body">
                    <?php _e('Modal content', 'rmcommon'); ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal"><?php _e('Close', 'rmcommon'); ?></button>
                </div>
            </div>
        </div>
    </div>
    <div class="cudialogs cu-modal fade" id="MyCuModal" tabindex="-1" role="dialog" aria-labelledby="mycu-modalLabel" aria-hidden="true">
        <div class="cu-dialog-dialog large">
            <div class="cu-dialog-content">
                <div class="cu-dialog-header cu-titlebar">
                    <button type="button" class="close" data-dismiss="cu-modal" aria-hidden="true">&times;</button>
                    <h4 class="cu-dialog-title" id="mycu-modalLabel"><?php _e('Modal Title', 'rmcommon'); ?></h4>
                </div>
                <div class="cu-dialog-body">
                    <?php _e('Dialog content', 'rmcommon'); ?>
                </div>
                <div class="cu-dialog-footer">
                    <button type="button" class="btn btn-primary" onclick="$('#MyCuModal').modal('hide');"><?php _e('Close', 'rmcommon'); ?></button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xs-12">
        <h2><strong><?php _e('Alerts', 'rmcommon'); ?></strong></h2>
        <div class="alert alert-success">.alert-success</div>
        <div class="alert alert-info">.alert-info</div>
        <div class="alert alert-warning">.alert-warning</div>
        <div class="alert alert-danger">.alert-danger</div>
    </div>
</div>

<div class="row">
    <div class="col-xs-12">
        <h2><strong><?php _e('Progess', 'rmcommon'); ?></strong></h2>
        <div class="progress">
            <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 40%">
                40% Complete (success)
            </div>
        </div>
        <div class="progress">
            <div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100" style="width: 20%">
                20% Complete (info)
            </div>
        </div>
        <div class="progress">
            <div class="progress-bar progress-bar-warning" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 60%">
                60% Complete (warning)
            </div>
        </div>
        <div class="progress">
            <div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100" style="width: 80%">
                80% Complete (danger)
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xs-12">
        <h2><strong><?php _e('Tables', 'rmcommon'); ?></strong></h2>
    </div>
</div>

<div class="row">
    <div class="col-sm-4">
        <table class="table">
            <thead>
            <tr>
                <th>#</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Username</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>1</td>
                <td>Mark</td>
                <td>Otto</td>
                <td>@mdo</td>
            </tr>
            <tr>
                <td>2</td>
                <td>Jacob</td>
                <td>Thornton</td>
                <td>@fat</td>
            </tr>
            <tr>
                <td>3</td>
                <td>Larry</td>
                <td>the Bird</td>
                <td>@twitter</td>
            </tr>
            </tbody>
        </table>
    </div>

    <div class="col-sm-4">
        <table class="table table-striped">
            <thead>
            <tr>
                <th>#</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Username</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>1</td>
                <td>Mark</td>
                <td>Otto</td>
                <td>@mdo</td>
            </tr>
            <tr>
                <td>2</td>
                <td>Jacob</td>
                <td>Thornton</td>
                <td>@fat</td>
            </tr>
            <tr>
                <td>3</td>
                <td>Larry</td>
                <td>the Bird</td>
                <td>@twitter</td>
            </tr>
            </tbody>
        </table>
    </div>

    <div class="col-sm-4">
        <table class="table table-condensed">
            <thead>
            <tr>
                <th>#</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Username</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>1</td>
                <td>Mark</td>
                <td>Otto</td>
                <td>@mdo</td>
            </tr>
            <tr>
                <td>2</td>
                <td>Jacob</td>
                <td>Thornton</td>
                <td>@fat</td>
            </tr>
            <tr>
                <td>3</td>
                <td colspan="2">Larry the Bird</td>
                <td>@twitter</td>
            </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="row">
    <div class="col-xs-12">
        <h2><strong><?php _e('Other Elements', 'rmcommon'); ?></strong></h2>
        <p>Simple text</p>
        <p><a href="#">Links</a></p>
        <p class="text-info">Information text</p>
        <p class="text-success">Success text</p>
        <p class="text-warning">Warning text</p>
        <p class="text-danger">Danger text</p>
        <p class="text-primary">Primary text</p>
        <p class="text-muted">Muted text</p>
        <p class="bg-primary">Nullam id dolor id nibh ultricies vehicula ut id elit.</p>
        <p class="bg-success">Duis mollis, est non commodo luctus, nisi erat porttitor ligula.</p>
        <p class="bg-info">Maecenas sed diam eget risus varius blandit sit amet non magna.</p>
        <p class="bg-warning">Etiam porta sem malesuada magna mollis euismod.</p>
        <p class="bg-danger">Donec ullamcorper nulla non metus auctor fringilla.</p>
        <p><code>Inline code</code></p>
        <pre>Preformatted
paragraph</pre>
    </div>
</div>
