.PHONY: analyse audit check coverage dist fix install lint test validate

install:
	composer install --no-interaction --prefer-dist

validate:
	composer validate --strict --no-check-lock

lint:
	composer lint

fix:
	composer fix

analyse:
	composer analyse

test:
	composer test

coverage:
	composer coverage

audit:
	composer audit

check: validate lint analyse test audit

dist:
	@test -n "$(VERSION)" || (echo "VERSION is required" >&2; exit 1)
	@printf '%s\n' "$(VERSION)" | grep -Eq '^[0-9]+\.[0-9]+\.[0-9]+$$' || (echo "VERSION must use X.Y.Z format" >&2; exit 1)
	mkdir -p artifacts
	composer archive --format=zip --dir=artifacts --file="fpdf-autofirma-$(VERSION)"
