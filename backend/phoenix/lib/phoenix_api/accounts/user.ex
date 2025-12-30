defmodule PhoenixApi.Accounts.User do
  use Ecto.Schema
  import Ecto.Changeset

  schema "users" do
    field :first_name, :string
    field :last_name, :string
    field :gender, :string
    field :birthdate, :date

    timestamps(type: :naive_datetime)
  end

  def changeset(user, attrs) do
    user
    |> cast(attrs, [:first_name, :last_name, :gender, :birthdate])
    |> validate_required([:first_name, :last_name, :gender, :birthdate])
    |> validate_inclusion(:gender, ["male", "female"])
  end
end