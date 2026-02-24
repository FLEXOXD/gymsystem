#!/usr/bin/env bash
set -u

APP_DIR="${APP_DIR:-/var/www/gymsystem/current}"
OK=0
FAIL=0

line() {
  printf '%s\n' "------------------------------------------------------------"
}

mark_ok() {
  echo "[OK]   $1"
  OK=$((OK + 1))
}

mark_fail() {
  echo "[FAIL] $1"
  FAIL=$((FAIL + 1))
}

check_service() {
  local svc="$1"
  if systemctl is-active --quiet "$svc"; then
    mark_ok "service $svc active"
  else
    mark_fail "service $svc inactive"
  fi
}

echo "GymSystem - 2 minute ops check"
echo "Time: $(date)"
line

echo "System usage"
free -h
df -h /
line

echo "Services"
check_service apache2
check_service redis-server
check_service laravel-worker@1
check_service laravel-worker@2
check_service laravel-worker@3
check_service laravel-worker@4
line

echo "Runtime"
if command -v php >/dev/null 2>&1; then
  php -v | head -n 1
else
  mark_fail "php command not found"
fi

if command -v redis-cli >/dev/null 2>&1; then
  if redis-cli ping 2>/dev/null | grep -q PONG; then
    mark_ok "redis ping PONG"
  else
    mark_fail "redis ping failed"
  fi
else
  mark_fail "redis-cli command not found"
fi
line

if [ -d "$APP_DIR" ] && [ -f "$APP_DIR/artisan" ]; then
  echo "Laravel checks ($APP_DIR)"
  if sudo -u www-data php "$APP_DIR/artisan" about >/dev/null 2>&1; then
    mark_ok "artisan about"
  else
    mark_fail "artisan about failed"
  fi

  if sudo -u www-data php "$APP_DIR/artisan" queue:failed --no-ansi 2>/dev/null | grep -q "No failed jobs"; then
    mark_ok "no failed jobs in queue"
  else
    echo "[INFO] queue:failed has entries or command output differs"
  fi
else
  mark_fail "APP_DIR invalid ($APP_DIR)"
fi
line

echo "Summary: $OK OK / $FAIL FAIL"
if [ "$FAIL" -gt 0 ]; then
  echo "Action needed: review failed checks above."
  exit 1
fi

echo "All critical checks passed."
exit 0

