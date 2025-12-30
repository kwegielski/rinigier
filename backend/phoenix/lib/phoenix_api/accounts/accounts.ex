defmodule PhoenixApi.Accounts do
  import Ecto.Query, warn: false
  alias PhoenixApi.Repo
  alias PhoenixApi.Accounts.User

  @default_page 1
  @default_limit 20

  def list_users(params \\ %{}) do
    query =
      User
      |> build_filter(params)
      |> build_sort(params)
      |> build_pagination(params)

    Repo.all(query)
  end

  def get_user!(id), do: Repo.get!(User, id)

  def create_user(attrs \\ %{}) do
    %User{}
    |> User.changeset(attrs)
    |> Repo.insert()
  end

  def update_user(%User{} = user, attrs) do
    user
    |> User.changeset(attrs)
    |> Repo.update()
  end


  def delete_user(%User{} = user), do: Repo.delete(user)

  defp build_filter(query, params) do
    query
    |> maybe_filter(:first_name, params)
    |> maybe_filter(:last_name, params)
    |> maybe_filter(:gender, params)
    |> maybe_filter_date(:birthdate, :from, params)
    |> maybe_filter_date(:birthdate, :to, params)
  end

  defp maybe_filter(query, _field, params) when params == %{}, do: query

  defp maybe_filter(query, field, params) do
    case Map.get(params, to_string(field)) do
      nil -> query
      value -> from u in query, where: ilike(field(u, ^field), ^"%#{value}%")
    end
  end

  defp maybe_filter_date(query, field, :from, params) do
    case Map.get(params, "birthdate_from") do
      nil -> query
      val -> from u in query, where: field(u, ^field) >= ^Date.from_iso8601!(val)
    end
  end

  defp maybe_filter_date(query, field, :to, params) do
    case Map.get(params, "birthdate_to") do
      nil -> query
      val -> from u in query, where: field(u, ^field) <= ^Date.from_iso8601!(val)
    end
  end

  defp build_sort(query, params) do
    sort_field = Map.get(params, "sort", "id")
    sort_direction = Map.get(params, "direction", "asc")

    {field_atom, dir_atom} = safe_sort(sort_field, sort_direction)
    from u in query, order_by: [{^dir_atom, field(u, ^field_atom)}]
  end

  defp safe_sort(field, dir) do
    field_atom =
      case field do
        "first_name" -> :first_name
        "last_name" -> :last_name
        "gender" -> :gender
        "birthdate" -> :birthdate
        _ -> :id
      end

    dir_atom =
      case dir do
        "desc" -> :desc
        _ -> :asc
      end

    {field_atom, dir_atom}
  end

  defp build_pagination(query, params) do
    page = Map.get(params, "page", Integer.to_string(@default_page)) |> String.to_integer()
    limit = Map.get(params, "limit", Integer.to_string(@default_limit)) |> String.to_integer()
    offset = (page - 1) * limit

    query
    |> limit(^limit)
    |> offset(^offset)
  end
end
