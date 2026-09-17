<?php
$pageTitle = 'Product Management';
require '../includes/admin-header.php';
$pdo = db();
if (($_GET['action'] ?? '') === 'delete' && ($id = (int) ($_GET['id'] ?? 0))) {
    $pdo->prepare('DELETE FROM products WHERE id=?')->execute([$id]);
    flash('success', 'T-shirt deleted.');
    go('admin/products.php');
}
if (($_GET['action'] ?? '') === 'set-status' && ($id = (int) ($_GET['id'] ?? 0))) {
    $status = $_GET['status'] ?? '';
    if (in_array($status, ['active', 'discontinued'], true)) {
        $pdo->prepare('UPDATE products SET status=? WHERE id=?')->execute([$status, $id]);
        flash('success', $status === 'active' ? 'Product continued and is now visible in the shop.' : 'Product discontinued and hidden from the shop.');
    }
    go('admin/products.php');
}
$products = $pdo->query('SELECT p.*,c.name category_name FROM products p JOIN categories c ON c.id=p.category_id ORDER BY p.created_at DESC')->fetchAll();
?>
<div class="d-flex justify-content-between mb-3"><p class="mb-0">Only oversized T-shirts are managed here.</p><a class="btn btn-dark" href="add-product.php">+ Add T-shirt</a></div>
<div class="card table-responsive"><table class="table align-middle mb-0"><thead><tr><th>Product</th><th>Collection</th><th>Price</th><th>Discount</th><th>Stock</th><th>Status</th><th></th></tr></thead><tbody><?php foreach($products as $p): ?><tr><td><img src="<?=e(base($p['image']))?>" width="42" height="42" class="me-2" style="object-fit:cover" alt=""><?=e($p['name'])?></td><td><?=e($p['category_name'])?></td><td><?=money($p['original_price'])?></td><td><?=$p['discount_percent']?>%</td><td><?=$p['stock_quantity']?></td><td><?=e($p['status'])?></td><td class="text-nowrap"><a class="btn btn-sm btn-outline-dark" href="edit-product.php?id=<?=$p['id']?>">Edit</a><?php if($p['status'] === 'discontinued'): ?> <a class="btn btn-sm btn-outline-success" href="products.php?action=set-status&id=<?=$p['id']?>&status=active">Continue</a><?php else: ?> <a class="btn btn-sm btn-outline-warning" data-confirm="Discontinue this T-shirt? It will be hidden from customers." href="products.php?action=set-status&id=<?=$p['id']?>&status=discontinued">Discontinue</a><?php endif ?> <a class="btn btn-sm btn-outline-danger" data-confirm="Delete this T-shirt?" href="products.php?action=delete&id=<?=$p['id']?>">Delete</a></td></tr><?php endforeach ?></tbody></table></div>
<?php require '../includes/admin-footer.php'; ?>
