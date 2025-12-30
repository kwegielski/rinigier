FROM elixir:1.16-alpine

RUN apk add --no-cache \
    build-base \
    git \
    npm \
    postgresql-client

WORKDIR /app

RUN mix local.hex --force \
    && mix local.rebar --force

EXPOSE 4000