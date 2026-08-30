.PHONY: audit check dist install validate

install:
	composer install --no-interaction --prefer-dist

validate:
	composer validate --strict --no-check-lock

audit:
	composer audit

check: validate audit

dist:
	@test -n "$(VERSION)" || (echo "VERSION is required" >&2; exit 1)
	@printf '%s\n' "$(VERSION)" | grep -Eq '^[0-9]+\.[0-9]+\.[0-9]+$$' || (echo "VERSION must use X.Y.Z format" >&2; exit 1)
	mkdir -p artifacts
	composer archive --format=zip --dir=artifacts --file="fpdf-autofirma-$(VERSION)"
