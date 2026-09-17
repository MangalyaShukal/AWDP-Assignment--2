<?php
require_once __DIR__ . '/../config/database.php';
if (session_status() !== PHP_SESSION_ACTIVE) session_start();

function e($value): string { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); }
function base(string $path = ''): string {
    return preg_match('#^https?://#i', $path) ? $path : '/OVERSO/' . ltrim($path, '/');
}
function go(string $path): never { header('Location: ' . base($path)); exit; }
function flash(string $key, ?string $message = null): ?string {
    if ($message !== null) { $_SESSION['flash'][$key] = $message; return null; }
    $message = $_SESSION['flash'][$key] ?? null; unset($_SESSION['flash'][$key]); return $message;
}
function user(): ?array { return $_SESSION['user'] ?? null; }
function is_logged_in(): bool { return isset($_SESSION['user']); }
function require_login(): void {
    if (!is_logged_in()) { flash('danger', 'Please log in to continue.'); go('user-login.php'); }
    $check = db()->prepare('SELECT status FROM users WHERE id=?'); $check->execute([$_SESSION['user']['id']]);
    if ($check->fetchColumn() !== 'active') { unset($_SESSION['user']); flash('danger', 'Your account is blocked. Please contact the administrator.'); go('user-login.php'); }
}
function require_admin(): void { if (empty($_SESSION['admin'])) { flash('danger', 'Unauthorized Access'); go('admin-login.php'); } }
function product_price(array $p): float { return round((float)$p['original_price'] * (100 - (float)$p['discount_percent']) / 100, 2); }
function cart_count(): int { return array_sum(array_column($_SESSION['cart'] ?? [], 'qty')); }
function money($amount): string { return '₹' . number_format((float)$amount, 2); }
function cart_items(): array {
    $cart = $_SESSION['cart'] ?? []; if (!$cart) return [];
    $ids = array_keys($cart); $marks = implode(',', array_fill(0, count($ids), '?'));
    $q = db()->prepare("SELECT p.*, c.name category_name FROM products p JOIN categories c ON c.id=p.category_id WHERE p.id IN ($marks)"); $q->execute($ids);
    $products = []; foreach ($q as $p) { $key=$p['id'].'|'.$cart[$p['id']]['size']; $p['size']=$cart[$p['id']]['size']; $p['qty']=$cart[$p['id']]['qty']; $p['unit_price']=product_price($p); $products[$key]=$p; }
    return $products;
}
function cart_totals(): array {
    $subtotal=0; foreach(cart_items() as $item) $subtotal += $item['unit_price']*$item['qty'];
    $coupon=$_SESSION['coupon'] ?? null; $couponDiscount=0;
    if ($coupon && $subtotal >= $coupon['minimum_order']) $couponDiscount = $coupon['discount_type']==='percent' ? min($subtotal*$coupon['discount_value']/100, $coupon['maximum_discount'] ?: PHP_FLOAT_MAX) : min($subtotal,$coupon['discount_value']);
    $tax=round(max(0,$subtotal-$couponDiscount)*.05,2); $delivery=$subtotal>999 ? 0 : ($subtotal ? 99 : 0);
    return compact('subtotal','couponDiscount','tax','delivery') + ['grand'=>$subtotal-$couponDiscount+$tax+$delivery];
}
function add_recent(int $id): void { $seen=$_SESSION['recently_viewed']??[]; $seen=array_values(array_diff($seen,[$id])); array_unshift($seen,$id); $_SESSION['recently_viewed']=array_slice($seen,0,6); }
